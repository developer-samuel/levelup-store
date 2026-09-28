# ──────────────────────────────────────────────────────────────────────────────
# 📦 Ansible / k3s Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: ansible-install k3s-install server-harden

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

## Harden server - SSH, fail2ban, auto security updates (IP from terraform output, key from .env)
server-harden:
	$(call require_bin,ansible-playbook,sudo apt install -y ansible)
	$(call get_vm_ip)
	cd $(ANSIBLE_DIR) && ansible-playbook \
		-i "$(VM_IP)," \
		-e "ansible_user=$(ANSIBLE_USER) ansible_ssh_private_key_file=$(ANSIBLE_SSH_KEY)" \
		playbooks/hardening.yml
