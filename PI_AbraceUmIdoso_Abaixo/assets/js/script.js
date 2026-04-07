
// Script simples para interações do layout e confirmação visual.
document.addEventListener('DOMContentLoaded', () => {
  const fadeEls = document.querySelectorAll('.fade-in');
  fadeEls.forEach((el, i) => setTimeout(() => el.classList.add('show'), 120 * i));

  const autoClose = document.querySelector('[data-auto-close="true"]');
  if (autoClose) {
    setTimeout(() => autoClose.remove(), 5000);
  }
});

function abrirPreviewCarta() {
  const modal = document.getElementById('previewModal');
  if (!modal) return;
  const texto = document.getElementById('textoCarta');
  const idoso = document.getElementById('idosoSelect');
  const conteudo = document.getElementById('previewMensagem');
  const destinatario = document.getElementById('previewDestinatario');
  conteudo.textContent = texto ? texto.value : '';
  destinatario.textContent = idoso && idoso.selectedIndex >= 0 ? idoso.options[idoso.selectedIndex].text : '';
  modal.style.display = 'flex';
}

function fecharPreviewCarta() {
  const modal = document.getElementById('previewModal');
  if (modal) modal.style.display = 'none';
}
