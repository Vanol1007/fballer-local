document.addEventListener("DOMContentLoaded", function () {
    let embeds = document.querySelectorAll("iframe");

    embeds.forEach(function (iframe) {
        let src = iframe.getAttribute("src");

		if  (src.includes("youtube.com") || src.includes("vimeo.com") ) {
            let wrapper = document.createElement("div");
            wrapper.classList.add("video-wrapper");

            let overlay = document.createElement("div");
            overlay.classList.add("video-overlay");
            overlay.onclick = function () {
                playEmbedVideo(overlay, iframe);
            };

            let playButton = document.createElement("div");
            playButton.classList.add("play-button");

            overlay.appendChild(playButton);

            // Оборачиваем iframe
            iframe.parentNode.insertBefore(wrapper, iframe);
            wrapper.appendChild(overlay);
            wrapper.appendChild(iframe);

            // Очищаем атрибут src, чтобы видео не загружалось сразу
            iframe.dataset.src = src;
            iframe.removeAttribute("src");
        }
    });
});

function playEmbedVideo(overlay, iframe) {
    let videoUrl = iframe.dataset.src + (iframe.dataset.src.includes("?") ? "&autoplay=1" : "?autoplay=1");
    iframe.setAttribute("src", videoUrl); // Вставляем ссылку с autoplay
    overlay.style.display = "none"; // Скрываем оверлей
}