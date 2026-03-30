function initSalaryRange() {
    const range = document.getElementById("range4");
    const output = document.getElementById("rangeValue");

    if (!range || !output) {
        return;
    }

    const renderValue = function () {
        output.textContent = range.value + " DT";
    };

    range.addEventListener("input", renderValue);
    range.addEventListener("change", renderValue);
    renderValue();
}
