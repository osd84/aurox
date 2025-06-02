// Gestionnaire de modales Bootstrap 5
class OsdModalAlert {
    // Constructeur
    constructor() {
        // count in

        if(document.getElementById('osd-modal-manager')) {
            console.log('ModalManager déjà initialisé');
            return;
        }

        const modalManagerHTML = `<div class="modal fade" id="osd-modal-manager" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">${title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
                    id="osd-modal-manager-close"
                    ></button>
                  </div>
                  <div class="modal-body">
                    <p>${message}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="osd-modal-manager-cancel" data-bs-dismiss="modal">${cancelText}</button>
                    <button type="button" class="btn ${btnClass}" id="osd-modal-manager-confirm">${confirmText}</button>
                  </div>
                </div>
              </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        this.modalDom = document.getElementById("osd-modal-manager");
        this.modalBs = new bootstrap.Modal(this.modalDom);
    }

    // Crée une modale de confirmation
    async confirm(title, message, options = {}) {
        const btnClass = options.btnClass || 'btn-primary';
        const confirmText = options.confirmText || 'Confirmer';
        const cancelText = options.cancelText || 'Annuler';

        document.getElementById(`osd-modal-manager-confirm`).classList.remove('btn-primary');
        document.getElementById(`osd-modal-manager-confirm`).classList.add(btnClass);

        document.getElementById(`osd-modal-manager-confirm`).textContent = confirmText;
        document.getElementById(`osd-modal-manager-cancel`).textContent = cancelText;

        return new Promise((resolve) => {

            document.getElementById(`osd-modal-manager-confirm`).addEventListener('click', function() {
                this.modalBs.hide();
                this.modalDom.remove();
                resolve(true);
            });

            document.getElementById(`osd-modal-manager-cancel`).addEventListener('click', function() {
                this.modalBs.hide();
                this.modalDom.remove();
                resolve(false);
            });
            document.getElementById(`osd-modal-manager-close`).addEventListener('click', function() {
                this.modalBs.hide();
                this.modalDom.remove();
                resolve(false);
            });

            this.modalBs.show();
        });
    }

    // Crée une modale d'alerte
    alert(title, message, options = {}) {
        const modalId = this.generateId('alert-modal');
        const btnClass = options.btnClass || 'btn-primary';
        const btnText = options.btnText || 'OK';

        return new Promise((resolve) => {
            const modalHTML = `
            <div class="modal fade" id="osd-modal-manager" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">${title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
                    id="osd-modal-manager-close">
                    
                </button>
                  </div>
                  <div class="modal-body">
                    <p>${message}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn ${btnClass}" data-bs-dismiss="modal" id="osd-modal-manager-confirm">${btnText}</button>
                  </div>
                </div>
              </div>
            </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const this.modalDom = document.getElementById(modalId);
            const modal = new bootstrap.Modal(this.modalDom);

            document.getElementById(`osd-modal-manager-close`).addEventListener('click', function() {
                this.modalBs.hide();
                this.modalDom.remove();
                resolve(false);
            });

            document.getElementById(`osd-modal-manager-confirm`).addEventListener('click', function() {
                this.modalBs.hide();
                this.modalDom.remove();
                resolve(true);
            });


            this.modalBs.show();
        });
    }

}

// Créer une instance globale
const modalManager = new OsdModalAlert();