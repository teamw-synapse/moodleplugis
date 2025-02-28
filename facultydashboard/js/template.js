let slideIndex = 1;

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
    slideIndex = Math.min(slideIndex + 1, numSlides - 1);
    slides.style.transform = `translateX(-${slideIndex * slideWidth}px)`;
  };