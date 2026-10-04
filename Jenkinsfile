// ──────────────────────────────────────────────────────────────────────────────
// Build + Deploy pipeline - mirrors .github/workflows/deploy.yml
// Triggered manually; builds Docker images, pushes to GHCR,
// updates Helm values → ArgoCD auto-syncs.
//
// Jenkins credentials required:
//   ghcr-token   - GitHub Container Registry token (Secret text)
//   github-pat   - GitHub PAT for pushing Helm tag commits (Username/Password)
//
// Jenkins global env required:
//   GHCR_USER    - GitHub username for GHCR login (e.g. developer-samuel)
//
// Parameters:
//   IMAGE_TAG    - leave empty to build from HEAD, or pass a SHA tag for rollback
//   TARGET       - all | ecommerce | assistant
//   ENVIRONMENT  - staging | production
// ──────────────────────────────────────────────────────────────────────────────

pipeline {
    agent any

    environment {
        REGISTRY  = 'ghcr.io'
        GHCR_REPO = 'developer-samuel/levelup-store'
    }

    parameters {
        string(
            name: 'IMAGE_TAG',
            defaultValue: '',
            description: 'Docker image tag (SHA) to deploy. Leave empty to build from HEAD.'
        )
        choice(
            name: 'TARGET',
            choices: ['all', 'ecommerce', 'assistant'],
            description: 'Which app to build and deploy.'
        )
        choice(
            name: 'ENVIRONMENT',
            choices: ['staging', 'production'],
            description: 'Target environment.'
        )
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Detect Changes') {
            steps {
                script {
                    def deploy = load 'jenkins/pipelines/deploy.groovy'
                    deploy.detectChanges()
                }
            }
        }

        stage('Build & Push') {
            // Skipped entirely on rollback (IMAGE_TAG already exists in GHCR)
            when { expression { params.IMAGE_TAG == '' } }
            parallel {

                stage('Ecommerce') {
                    when { expression { env.ECOMMERCE_CHANGED == 'true' && params.TARGET != 'assistant' } }
                    steps {
                        script {
                            def deploy = load 'jenkins/pipelines/deploy.groovy'
                            deploy.buildAndPush('ecommerce')
                        }
                    }
                }

                stage('Assistant') {
                    when { expression { env.ASSISTANT_CHANGED == 'true' && params.TARGET != 'ecommerce' } }
                    steps {
                        script {
                            def deploy = load 'jenkins/pipelines/deploy.groovy'
                            deploy.buildAndPush('assistant')
                        }
                    }
                }

            }
        }

        stage('Deploy → Staging') {
            steps {
                script {
                    def deploy = load 'jenkins/pipelines/deploy.groovy'
                    deploy.syncImageTags('staging')
                }
            }
        }

        stage('Approval') {
            when { expression { params.ENVIRONMENT == 'production' } }
            steps {
                timeout(time: 30, unit: 'MINUTES') {
                    input message: "Deploy ${env.IMAGE_TAG} to production?", ok: 'Deploy'
                }
            }
        }

        stage('Deploy → Production') {
            when { expression { params.ENVIRONMENT == 'production' } }
            steps {
                script {
                    def deploy = load 'jenkins/pipelines/deploy.groovy'
                    deploy.syncImageTags('production')
                }
            }
        }

    }

    post {
        success { echo "✓ Deploy ${env.IMAGE_TAG} to ${params.ENVIRONMENT} complete - ArgoCD will sync." }
        failure { echo "✗ Pipeline failed." }
    }
}
