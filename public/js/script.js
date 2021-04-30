let loader = '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';

let characterDiv = document.getElementById("characters");
let currentCharacter = characterDiv ? characterDiv.getAttribute('data-current') : '';

window.onload = function () {
    let characterXhr = new XMLHttpRequest();

    if (characterDiv) {
        characterDiv.innerHTML = ''; // Remove loader

        characterXhr.onload = function () {
            let data = JSON.parse(characterXhr.responseText);

            data['characters'].forEach(function (character) {
                let link = document.createElement("a");
                let image = document.createElement("img");

                image.src = character.image;
                image.title = character.name;

                if (character.name === currentCharacter) {
                    image.classList.add('character-icon', 'icon-light-shadow', 'character-selected')
                } else if (currentCharacter === '') { // no selected characters
                    image.classList.add('character-icon', 'icon-dark-shadow')
                } else { // display unselected characters with filter
                    image.classList.add('character-icon', 'icon-dark-shadow', 'character-gray-filter')
                }

                link.href = character.url;
                link.appendChild(image);

                characterDiv.appendChild(link);
            });
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
            let data = JSON.parse(countXhr.responseText);

            input.placeholder = 'Rechercher parmi près de ' + data + ' répliques...';
        };

        countXhr.open('GET', input.getAttribute('data-url'));
        countXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        countXhr.send();
    }
};

// ==================================================

function addOpenModalEvent(squareButtons) {
    squareButtons.forEach(function (element) {
        element.addEventListener('click', function () {
            let dataId = element.getAttribute('data-id');

            document.getElementById(dataId + "-modal-img").src = document.getElementById(dataId + "-img").getAttribute('data-img');
            document.getElementById(dataId + "-modal").style.display = "block";
        });
    });
}

function addCloseModalEvent(modalCloseButtons) {
    modalCloseButtons.forEach(function (element) {
        element.addEventListener('click', function () {
            element.parentNode.parentNode.style.display = "none";
        });
    });
}

window.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal-background').forEach(function (element) {
            element.style.display = "none";
        });
    }
})

function addSharingEvent(sharingButtons) {
    sharingButtons.forEach(function (element) {
        element.addEventListener('click', function () {
            let button = this.parentNode.getElementsByTagName('button')[0];

            navigator.clipboard.writeText(button.value);

            let title = button.getAttribute('data-title');
            let notification = document.getElementById("notification");
            let text = title + ' copié !';

            if (button.getAttribute('data-type') === 'social') {
                text = 'Lien optimisé pour ' + title + ' copié !';
            }

            notification.innerText = text;
            notification.style.display = "block";

            setTimeout(function Remove() {
                notification.style.display = "none";
            }, 1500);
        });
    });
}

addOpenModalEvent(document.querySelectorAll('.square_btn'));
addCloseModalEvent(document.querySelectorAll('.modal-close'));
addSharingEvent(document.querySelectorAll('.sharing-btn'));

// ==================================================

let timer;

// No autocomplete for GIF page (without characters) and Character (selected) page
if (characterDiv && !currentCharacter) {
    document.getElementById('quotes').addEventListener("keyup", function (e) {
        e.preventDefault();

        let url = this.getAttribute('data-search-url')
        let value = this.value;

        clearInterval(timer);

        document.getElementById('gif-list').innerHTML = loader;

        timer = setTimeout(function () {
            let searchXhr = new XMLHttpRequest();

            searchXhr.onload = function () {
                document.getElementById('gif-list').innerHTML = searchXhr.responseText;

                addOpenModalEvent(document.querySelectorAll('.square_btn'));
                addCloseModalEvent(document.querySelectorAll('.modal-close'));
                addSharingEvent(document.querySelectorAll('.sharing-btn'));
            };

            searchXhr.open('GET', url + '?search=' + value);
            searchXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
            searchXhr.send();
        }, 650);
    });
}
