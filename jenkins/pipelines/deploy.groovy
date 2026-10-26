// Build + Deploy pipeline logic

def detectChanges() {
    sh 'chmod +x jenkins/steps/detect-changes.sh'
    sh 'jenkins/steps/detect-changes.sh'
    def props = readProperties file: 'jenkins.env'
    env.ECOMMERCE_CHANGED = props.ECOMMERCE_CHANGED
    env.ASSISTANT_CHANGED = props.ASSISTANT_CHANGED
    env.TRAEFIK_CHANGED = props.TRAEFIK_CHANGED
    env.IMAGE_TAG = params.IMAGE_TAG ?: sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
    echo "Ecommerce changed: ${env.ECOMMERCE_CHANGED}"
    echo "Assistant changed: ${env.ASSISTANT_CHANGED}"
    echo "Traefik changed: ${env.TRAEFIK_CHANGED}"
    echo "Image tag: ${env.IMAGE_TAG}"
}

def buildAndPush(String app) {
    def image = "${env.REGISTRY}/${env.GHCR_REPO}-${app}:${env.IMAGE_TAG}"
    def assistantUrl = params.ENVIRONMENT == 'production' ? "https://assistant.${env.APP_DOMAIN}" : "https://assistant-staging.${env.APP_DOMAIN}"
    def buildArgs = app == 'ecommerce' ? "--build-arg VITE_API_URL=${assistantUrl}" : ''
    sh """
        docker build \
            --target ${app} \
            --tag ${image} \
            --file infrastructure/docker/Dockerfile.prod \
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

def buildAndPushTraefik() {
    def image = "${env.REGISTRY}/${env.GHCR_REPO}-traefik:${env.IMAGE_TAG}"
    sh """
        docker buildx build \
            --platform linux/amd64,linux/arm64 \
            --tag ${image} \
            --file infrastructure/docker/Dockerfile.traefik \
            --cache-from ${env.REGISTRY}/${env.GHCR_REPO}-traefik:buildcache \
            infrastructure/docker \
            --push
    """
    withCredentials([string(credentialsId: 'ghcr-token', variable: 'GHCR_TOKEN')]) {
        sh "echo \$GHCR_TOKEN | docker login ${env.REGISTRY} -u \$GHCR_USER --password-stdin"
        sh "docker buildx imagetools create -t ${env.REGISTRY}/${env.GHCR_REPO}-traefik:latest ${image}"
    }
}

def syncImageTags(String environment) {
    def valuesFile = environment == 'production' ? 'values.prod.yaml' : 'values.staging.yaml'

    def updateEcommerce = env.ECOMMERCE_CHANGED == 'true' || params.TARGET == 'ecommerce' || params.TARGET == 'all'
    def updateAssistant = env.ASSISTANT_CHANGED == 'true' || params.TARGET == 'assistant' || params.TARGET == 'all'
    def updateTraefik = env.TRAEFIK_CHANGED == 'true' || params.TARGET == 'traefik' || params.TARGET == 'all'

    def targetBranch = environment == 'production' ? 'main' : 'staging'

    withCredentials([usernamePassword(credentialsId: 'github-pat', usernameVariable: 'GIT_USER', passwordVariable: 'GIT_TOKEN')]) {
        sh """
            git config user.name  "jenkins[bot]"
            git config user.email "jenkins[bot]@noreply"
            git config credential.helper '!f() { echo username=\$GIT_USER; echo password=\$GIT_TOKEN; }; f'
            git fetch origin ${targetBranch}
            git checkout -f -B ${targetBranch} origin/${targetBranch}
        """

        if (updateEcommerce) {
            sh "yq e '.app.image.tag = \"${env.IMAGE_TAG}\"' -i infrastructure/helm/apps/ecommerce/${valuesFile}"
        }
        if (updateAssistant) {
            sh "yq e '.app.image.tag = \"${env.IMAGE_TAG}\"' -i infrastructure/helm/apps/assistant/${valuesFile}"
        }
        if (updateTraefik) {
            sh "yq e '.spec.valuesContent |= sub(\"tag: .*\", \"tag: ${env.IMAGE_TAG}\")' -i infrastructure/kubernetes/cluster/traefik-config.yaml"
        }

        sh """
            git add infrastructure/helm/apps/ecommerce/${valuesFile} \
                    infrastructure/helm/apps/assistant/${valuesFile} \
                    infrastructure/kubernetes/cluster/traefik-config.yaml
            git diff --cached --quiet && echo "No changes to commit" && exit 0
            git commit -m "chore: deploy ${env.IMAGE_TAG} to ${environment} [skip ci]"
            git push origin ${targetBranch}
        """
    }
}

return this
