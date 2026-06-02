const sliderAbout = () => {
  let slider = document.querySelector('.place-slider');

  if (slider) {
    var main = new Splide('.place-slider', {
      type: 'slide',
      // rewind    : true,
      pagination: false,
      arrows: true,
      perPage: 1,
      breakpoints: {
        992: {
          // autoWidth: false,
          perPage: 1,
          gap: 17,
        },

        670: {
          autoWidth: true,
          perPage: 1,
          gap: 17,
        },
      },
    });

    var thumbnails = new Splide('.thumb-place-slider', {
      gap: 21,
      pagination: false,
      isNavigation: true,
      arrows: false,
      perPage: 3,

    });

    main.sync(thumbnails);
    main.mount();
    thumbnails.mount();
  }


};

document.addEventListener('DOMContentLoaded', sliderAbout);


const galleryWrapper = () => {

  let container = document.querySelector('.single-news__content-inner');


  if (container) {


    let arrImg = container.querySelectorAll('div img');

    let arrWrap = container.querySelectorAll('div');


    //cycle for images containers
    for (let i = 0; i < arrWrap.length; i++) {
      //creating a gallery class and attribute to use dynamicly fancybox
      arrWrap[i].classList.add(`post-gallery`);
      arrWrap[i].setAttribute(`data-post-image`, `post-gallery-${i + 1}`);
      let galleryContainerarrWrap = arrWrap[i].getAttribute('data-post-image');

      //cycle for images links
      for (let i = 0; i < arrImg.length; i++) {
        //creating an images links wrappers and sett attrs and clsses
        let wrapper = document.createElement('a');
        wrapper.classList.add('post-gallery__image');
        wrapper.setAttribute('data-fancybox', `gal-${galleryContainerarrWrap}`);
        //get src from image and set this src to link fancybox
        let src = arrImg[i].getAttribute('src');
        wrapper.setAttribute('href', `${src}`);

        //append the wrappers to img tags
        arrImg[i].parentNode.insertBefore(wrapper, arrImg[i]);
        wrapper.appendChild(arrImg[i])
      }
    }
  }

}

document.addEventListener('DOMContentLoaded', galleryWrapper);



function applyPhoneMask() {
  document.querySelectorAll('#place-phone').forEach(phoneInput => {
      phoneInput.addEventListener('input', function () {
          let input = phoneInput.value.replace(/\D/g, ''); // Удаляем всё, кроме цифр

          // Добавляем префикс "+7" и ограничиваем ввод 11 цифрами
          if (input.length > 0) {
              input = '7' + input.substring(1, 11);
          }

          // Формируем маску поэтапно
          let formattedInput = '+7 (';
          if (input.length > 1) {
              formattedInput += input.substring(1, 4);
          }
          if (input.length >= 5) {
              formattedInput += ') ' + input.substring(4, 7);
          }
          if (input.length >= 8) {
              formattedInput += '-' + input.substring(7, 9);
          }
          if (input.length >= 10) {
              formattedInput += '-' + input.substring(9, 11);
          }

          phoneInput.value = formattedInput;
      });
  });
}

document.addEventListener('DOMContentLoaded', applyPhoneMask);



Fancybox.bind("[data-fancybox]", {
  // Your custom options
});
