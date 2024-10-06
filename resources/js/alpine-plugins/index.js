import Datatables from "./datatables.js";

document.addEventListener('alpine:init', () => {
    Alpine.plugin([
        Datatables,
    ]);
});
