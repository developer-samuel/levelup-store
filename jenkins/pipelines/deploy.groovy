// Build + Deploy pipeline logic

def detectChanges() {
    sh 'chmod +x jenkins/steps/detect-changes.sh'
    sh 'jenkins/steps/detect-changes.sh'
    def props = readProperties file: 'jenkins.env'
    env.ECOMMERCE_CHANGED = props.ECOMMERCE_CHANGED
    env.ASSISTANT_CHANGED = props.ASSISTANT_CHANGED
    env.IMAGE_TAG = params.IMAGE_TAG ?: sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
    echo "Ecommerce changed: ${env.ECOMMERCE_CHANGED}"
    echo "Assistant changed: ${env.ASSISTANT_CHANGED}"
    echo "Image tag: ${env.IMAGE_TAG}"
}

def buildAndPush(String app) {
    def image = "${env.REGISTRY}/${env.GHCR_REPO}-${app}:${env.IMAGE_TAG}"
    def buildArgs = app == 'ecommerce' ? "--build-arg VITE_API_URL=https://assistant.${env.APP_DOMAIN}" : ''
    sh """
        docker build \
            --target ${app} \
            --tag ${image} \
            --file docker/Dockerfile.prod \
            --cache-from ${env.REGISTRY}/${env.GHCR_REPO}-${app}:buildcache \
            ${buildArgs} \
            .
    """
    withCredentials([string(credentialsId: 'ghcr-token', variable: 'GHCR_TOKEN')]) {
        sh "echo \$GHCR_TOKEN | docker login ${env.REGISTRY} -u \$GHCR_USER --password-stdin"
        sh "docker push ${image}"
        sh "docker tag ${image} ${env.REGISTRY}/${env.GHCR_REPO}-${app}:latest"
        sh "docker push ${env.REGISTRY}/${env.GHCR_REPO}-${app}:latest"
    }
}

def syncImageTags(String environment) {
    def valuesFile = environment == 'production' ? 'values.prod.yaml' : 'values.staging.yaml'

    def updateEcommerce = env.ECOMMERCE_CHANGED == 'true' || params.TARGET == 'ecommerce' || params.TARGET == 'all'
    def updateAssistant = env.ASSISTANT_CHANGED == 'true' || params.TARGET == 'assistant' || params.TARGET == 'all'

    if (updateEcommerce) {
        sh "yq e '.app.image.tag = \"${env.IMAGE_TAG}\"' -i infrastructure/helm/apps/ecommerce/${valuesFile}"
    }
    if (updateAssistant) {
        sh "yq e '.app.image.tag = \"${env.IMAGE_TAG}\"' -i infrastructure/helm/apps/assistant/${valuesFile}"
    }

    withCredentials([usernamePassword(credentialsId: 'github-pat', usernameVariable: 'GIT_USER', passwordVariable: 'GIT_TOKEN')]) {
        sh """
            git config user.name  "jenkins[bot]"
            git config user.email "jenkins[bot]@noreply"
            git add infrastructure/helm/apps/ecommerce/${valuesFile} \
                    infrastructure/helm/apps/assistant/${valuesFile}
            git diff --cached --quiet && echo "No changes to commit" && exit 0
            git commit -m "chore: deploy ${env.IMAGE_TAG} to ${environment} [skip ci]"
            git fetch origin main
            git restore .
            git rebase origin/main
            git push https://\$GIT_USER:\$GIT_TOKEN@github.com/${env.GHCR_REPO}.git HEAD:main
        """
    }
}

return this
