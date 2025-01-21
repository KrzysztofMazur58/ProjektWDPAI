function typeText(element, text, speed) {
    let index = 0;
    const interval = setInterval(() => {
        element.textContent += text[index];
        index++;
        if (index === text.length) {
            clearInterval(interval);
        }
    }, speed);
}

window.addEventListener("DOMContentLoaded", () => {
    const headerElement = document.querySelector(".header h1");
    const text = headerElement.textContent;
    headerElement.textContent = "";

    setTimeout(() => {
        typeText(headerElement, text, 200);
    }, 600);
});

