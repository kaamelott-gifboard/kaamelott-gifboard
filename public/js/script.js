window.onload = function(){
    let characterXhr = new XMLHttpRequest();
    let characterDiv = document.getElementById("characters");

    characterXhr.onload = function () {
        if (characterXhr.status >= 200 && characterXhr.status < 300) {
            let data = JSON.parse(characterXhr.responseText);

            data['characters'].forEach(function (character) {
                let link = document.createElement("a");
                let image = document.createElement("img");

                image.src = window.location.origin.concat('/', character.image);
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

    // ==================================================

    let countXhr = new XMLHttpRequest();
    let input = document.getElementById("quotes");

    countXhr.onload = function () {
        if (countXhr.status >= 200 && countXhr.status < 300) {
            let data = JSON.parse(countXhr.responseText);

            input.placeholder = 'Parmi près de '.concat(data, ' répliques...');
        } else {
            // @todo: handle error
        }
    };

    countXhr.open('GET', input.getAttribute('data-url'));
    countXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    countXhr.send();
};

// ==================================================

function showGif(e) {
    document.getElementById("modal").style.display = "block";

    let gif = document.getElementById(e.getAttribute('data-id'));

    document.getElementById("modal-img").src = gif.getAttribute('data-img');
    document.getElementById("modal-quote").innerText = gif.getAttribute('data-quote');
    document.getElementById("modal-link").innerText = gif.getAttribute('data-url');
}

// ==================================================

let modal = document.getElementById("modal");

document.getElementsByClassName("modal-close")[0].onclick = function() {
    modal.style.display = "none";
}

window.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        modal.style.display = 'none'
    }
})

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
