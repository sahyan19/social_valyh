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