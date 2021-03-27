window.onload = function(){
    let characterXhr = new XMLHttpRequest();
    let characterDiv = document.getElementById("characters");

    if (characterDiv) {
        characterXhr.onload = function () {
            if (characterXhr.status >= 200 && characterXhr.status < 300) {
                let data = JSON.parse(characterXhr.responseText);

                data['characters'].forEach(function (character) {
                    let link = document.createElement("a");
                    let image = document.createElement("img");

                    image.src = character.image;
                    image.title = character.name;

                    if (character.name === characterDiv.getAttribute('data-current')) {
                        image.classList.add('character-icon', 'icon-light-shadow')
                    } else {
                        image.classList.add('character-icon', 'icon-dark-shadow')
                    }

                    link.href = character.url;
                    link.appendChild(image);

                    characterDiv.appendChild(link);
                });
            } else {
                // @todo: handle error
            }
        };

        characterXhr.open('GET', characterDiv.getAttribute('data-url'));
        characterXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        characterXhr.send();
    }

    // ==================================================

    let countXhr = new XMLHttpRequest();
    let input = document.getElementById("quotes");

    if (input) {
        countXhr.onload = function () {
            if (countXhr.status >= 200 && countXhr.status < 300) {
                let data = JSON.parse(countXhr.responseText);

                input.placeholder = 'Recherche parmi près de '.concat(data, ' répliques...');
            } else {
                // @todo: handle error
            }
        };

        countXhr.open('GET', input.getAttribute('data-url'));
        countXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        countXhr.send();
    }
};

// ==================================================

document.querySelectorAll('.square_btn').forEach(function(element) {
    element.addEventListener('click', function() {
        let image = document.getElementById(element.getAttribute('data-id') + "-img").getAttribute('data-img');

        document.getElementById(element.getAttribute('data-id') + "-modal-img").src = image;
        document.getElementById(element.getAttribute('data-id') + "-modal").style.display = "block";
    });
});

document.querySelectorAll('.modal-close').forEach(function(element) {
    element.addEventListener('click', function() {
        element.parentNode.parentNode.style.display = "none";
    });
});

window.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal-background').forEach(function(element) {
            element.style.display = "none";
        });
    }
})

// ==================================================

document.querySelectorAll('.copy-btn').forEach(function(element) {
    element.addEventListener('click', function() {
        let input = this.parentNode.getElementsByTagName('input')[0];

        navigator.clipboard.writeText(input.value);

        let notification = document.getElementById("notification");

        notification.innerText = 'Lien copié !';
        notification.style.display = "block";

        setTimeout(function Remove() {
            notification.style.display = "none";
        }, 1500);
    });
});
