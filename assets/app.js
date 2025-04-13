/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

let loader = `
    <div class="flex space-x-2 justify-center py-4">
        <div class="h-3 w-3 bg-yellow-700 rounded-full animate-[bounce_1s_infinite_100ms]"></div>
        <div class="h-3 w-3 bg-yellow-700 rounded-full animate-[bounce_1s_infinite_300ms]"></div>
        <div class="h-3 w-3 bg-yellow-700 rounded-full animate-[bounce_1s_infinite_500ms]"></div>
    </div>`;

let characterDiv = document.getElementById("characters");
let currentCharacter = characterDiv ? characterDiv.getAttribute('data-current') : '';

window.onload = function () {
    let characterXhr = new XMLHttpRequest();

    if (characterDiv) {
        characterDiv.innerHTML = '';

        characterXhr.onload = function () {
            let data = JSON.parse(characterXhr.responseText);

            data['characters'].forEach(function (character) {
                const link = document.createElement("a");
                link.href = character.url;
                link.className = 'w-11';

                const image = document.createElement("img");
                image.src = character.image;
                image.alt = character.name;
                image.title = character.name;
                image.className = [
                    'w-11',
                    'h-11',
                    'rounded-full',
                    'object-cover',
                    'transition-all',
                    'duration-150',
                    'border',
                    'shadow-md',
                    'hover:scale-110',
                    character.name === currentCharacter
                        ? ''
                        : currentCharacter
                            ? 'grayscale opacity-80 hover:opacity-100 hover:grayscale-0'
                            : '',
                ].join(' ');

                link.appendChild(image);
                characterDiv.appendChild(link);
            });
        };

        characterXhr.open('GET', characterDiv.getAttribute('data-url'));
        characterXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        characterXhr.send();
    }

    let countXhr = new XMLHttpRequest();
    let input = document.getElementById("quotes");

    if (input) {
        countXhr.onload = function () {
            let data = JSON.parse(countXhr.responseText);

            input.placeholder = 'Rechercher parmi ' + data + ' répliques...';
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

// ==================================================

let timer;

// No autocomplete for GIF page (without characters) and Character (selected) page
if (characterDiv && !currentCharacter) {
    document.getElementById('quotes').addEventListener("keyup", function (e) {
        e.preventDefault();

        let url = this.getAttribute('data-search-url')
        let value = this.value;

        if (value.length < 3 && value.length !== 0) {
            return;
        }

        clearInterval(timer);

        document.getElementById('gif-ul').innerHTML = loader;

        timer = setTimeout(function () {
            let searchXhr = new XMLHttpRequest();

            searchXhr.onload = function () {
                document.getElementById('gif-ul').innerHTML = searchXhr.responseText;

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

// ==================================================

let isFetching = false;
let currentPage = 2;
let canScroll = true;

const fetchGifs = async () => {
    isFetching = true;

    let searchXhr = new XMLHttpRequest();

    searchXhr.onload = function () {
        if (searchXhr.status === 404) {
            canScroll = false;
        }

        updateDom(searchXhr.responseText);

        currentPage++;
        document.getElementById('gif-ul-loader').innerHTML = '';
        isFetching = false;
    };

    let url = document.getElementById("gif-ul").getAttribute('data-url') + '?page=' + currentPage;

    searchXhr.open('GET', url);
    searchXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    searchXhr.send();
};

const updateDom = (gifs) => {
    document.getElementById("gif-ul").innerHTML += gifs;

    addOpenModalEvent(document.querySelectorAll('.square_btn'));
    addCloseModalEvent(document.querySelectorAll('.modal-close'));
    addSharingEvent(document.querySelectorAll('.sharing-btn'));
};

window.addEventListener("scroll", async () => {
    if (window.location.pathname !== '/') return;

    if (isFetching) return;

    if (!canScroll) return;

    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
        document.getElementById('gif-ul-loader').innerHTML = loader;
        await fetchGifs();
    }
});

addOpenModalEvent(document.querySelectorAll('.square_btn'));
addCloseModalEvent(document.querySelectorAll('.modal-close'));
addSharingEvent(document.querySelectorAll('.sharing-btn'));
