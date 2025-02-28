let slideIndex = 0;
  const slidePrev = (tabId) => {
    const slides = document.querySelector(`#${tabId} .tcourse-slides`);
    const slideWidth = slides.querySelector(".tcourse-card").offsetWidth;
    slideIndex = Math.max(slideIndex - 1, 0);
    slides.style.transform = `translateX(-${slideIndex * slideWidth}px)`;
  };

    const slideNext = (tabId) => {
    const slides = document.querySelector(`#${tabId} .tcourse-slides`);
    const slideWidth = slides.querySelector(".tcourse-card").offsetWidth;
    const numSlides = slides.children.length;
    slideIndex = Math.min(slideIndex + 3, numSlides - 3);
    slides.style.transform = `translateX(-${slideIndex * slideWidth}px)`;
  };