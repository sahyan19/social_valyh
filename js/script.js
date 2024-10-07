function toggleDetails(id, type = 'post') {
    const detailsDiv = document.getElementById(type === 'post' ? 'details-' + id : 'details-comment-' + id);
    detailsDiv.style.display = detailsDiv.style.display === 'none' ? 'block' : 'none';
}


function get_xml_http_request() {
    var xhr = null;

    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }

    return xhr;
}

function update_post_reactions(post_id, reaction) {
    var xhr = get_xml_http_request();

    if (xhr === null) {
        return;
    }

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var reaction_div = document.getElementById('reactions-' + post_id);
            reaction_div.innerHTML = `
                <i class="fas fa-thumbs-up reaction-like" title="Like"></i> ${response.like || 0}
                <i class="fas fa-heart reaction-love" title="Love"></i> ${response.love || 0}
                <i class="fas fa-surprise reaction-wow" title="Wow"></i> ${response.wow || 0}
                <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> ${response.sad || 0}
                <i class="fas fa-angry reaction-angry" title="Angry"></i> ${response.angry || 0}
            `;
        }
    };

    xhr.open('POST', 'update_reactions.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('post_id=' + post_id + '&reaction=' + reaction);
}


function update_comment_reactions(comment_id, reaction) {
    var xhr = get_xml_http_request();

    if (xhr === null) {
        return;
    }

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var reaction_div = document.getElementById('reactions-comment-' + comment_id);
            reaction_div.innerHTML = `
                <i class="fas fa-thumbs-up reaction-like" title="Like"></i> ${response.like || 0}
                <i class="fas fa-heart reaction-love" title="Love"></i> ${response.love || 0}
                <i class="fas fa-surprise reaction-wow" title="Wow"></i> ${response.wow || 0}
                <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> ${response.sad || 0}
                <i class="fas fa-angry reaction-angry" title="Angry"></i> ${response.angry || 0}
            `;
        }
    };

    xhr.open('POST', 'update_reactions.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('comment_id=' + comment_id + '&reaction=' + reaction);
}

document.addEventListener('DOMContentLoaded', function () {
    // Gestion de l'ajout d'une publication
    const postForm = document.getElementById('post-form');
    postForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(postForm);

        fetch('home.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const postContainer = document.createElement('div');
            postContainer.id = `post-${data.id}`;
            postContainer.innerHTML = `
                <h3 class="head-post"><img src="../img/imageTeste.png" alt="photo de profil" class="image-gros">${data.email} a publié :</h3>
                <p>${data.content}</p>
                <small>Publié le ${data.created_at}</small>
                <h3>Commentaires :</h3>
                <div id="comments-${data.id}"></div>
                <form class="comment-form" data-post-id="${data.id}">
                    <textarea name="comment_content" placeholder="Votre commentaire..." required></textarea>
                    <button type="submit" class="comment-box"><img src="../img/envoyer.png" alt="envoyer" class="envoyer"></button>
                </form>
            `;
            document.querySelector('.content').prepend(postContainer);
            postForm.reset();
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Gestion de l'ajout d'un commentaire
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(form);
            formData.append('post_id', form.dataset.postId);

            fetch('home.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const commentsDiv = document.getElementById(`comments-${data.post_id}`);
                const commentContainer = document.createElement('div');
                commentContainer.innerHTML = `
                    <div style="margin-left: 20px;">
                        <div class="comment-item">
                            <div class="comment-inside">
                                <img src="../img/imageTeste.png" alt="Photo de profil" class="image-mini">
                                <strong>${data.email} :</strong>
                            </div>
                            <p>${data.content}</p>
                            <small>Commenté le ${data.created_at}</small>
                        </div>
                    </div>
                `;
                commentsDiv.appendChild(commentContainer);
                form.reset();
            })
            .catch(error => console.error('Erreur:', error));
        });
    });
});