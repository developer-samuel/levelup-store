# ──────────────────────────────────────────────────────────────────────────────
# 📦 Ansible / k3s Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: ansible-install k3s-install server-harden kubeconfig kubectl-check

## Install Ansible collections (run once before k3s-install)
ansible-install:
	$(call require_bin,ansible-galaxy,sudo apt install -y ansible)
	cd $(ANSIBLE_DIR) && ansible-galaxy collection install -r requirements.yml

## Install k3s on server via Ansible (IP from terraform output, key from .env)
k3s-install:
	$(call require_bin,ansible-playbook,sudo apt install -y ansible)
	$(call get_vm_ip)
	cd $(ANSIBLE_DIR) && ansible-playbook \
		-i "$(VM_IP)," \
		-e "ansible_user=$(ANSIBLE_USER) ansible_ssh_private_key_file=$(ANSIBLE_SSH_KEY)" \
		playbooks/k3s.yml

## Fetch kubeconfig from server and save to ~/.kube/config-levelup (run once on new machine)
kubeconfig:
	$(call get_vm_ip)
	@mkdir -p ~/.kube
	ssh -i $(ANSIBLE_SSH_KEY) $(ANSIBLE_USER)@$(VM_IP) "sudo cat /etc/rancher/k3s/k3s.yaml" \
		| sed 's/127.0.0.1/$(VM_IP)/g' > ~/.kube/config-levelup
	@chmod 600 ~/.kube/config-levelup
	@grep -q 'KUBECONFIG=~/.kube/config-levelup' ~/.bashrc || echo 'export KUBECONFIG=~/.kube/config-levelup' >> ~/.bashrc
	@echo "✓ Kubeconfig saved to ~/.kube/config-levelup"
	@echo "  Run 'source ~/.bashrc' or open a new terminal to activate KUBECONFIG."

## Verify kubectl is connected to the correct cluster
kubectl-check:
	@kubectl config view --minify | grep server
	@kubectl get nodes

## Harden server - SSH, fail2ban, auto security updates (IP from terraform output, key from .env)
server-harden:
	$(call require_bin,ansible-playbook,sudo apt install -y ansible)
	$(call get_vm_ip)
	cd $(ANSIBLE_DIR) && ansible-playbook \
		-i "$(VM_IP)," \
		-e "ansible_user=$(ANSIBLE_USER) ansible_ssh_private_key_file=$(ANSIBLE_SSH_KEY)" \
		playbooks/hardening.yml
