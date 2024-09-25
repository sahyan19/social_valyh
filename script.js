
function toggleDetails(id, type) {
    var details = document.getElementById('details-' + (type === 'comment' ? 'comment-' : '') + id);
    details.style.display = (details.style.display === 'none' || details.style.display === '') ? 'block' : 'none';
}