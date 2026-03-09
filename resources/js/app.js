//import './bootstrap';

/*
import 'bootstrap';          // Bootstrap 5.2.3 JS
import $ from 'jquery';

window.$ = window.jQuery = $;


*/

import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
import Chart from 'chart.js/auto';
import TomSelect from "tom-select";
import 'tom-select/dist/css/tom-select.min.css';


import $ from 'jquery';

// Expose bootstrap globally for inline scripts
window.bootstrap = bootstrap;

//jquerry
window.$ = window.jQuery = $;

// Expose Chart globally for inline scripts
window.Chart = Chart;

// import the module but don't auto-run
import { initDataTable } from './datatable';
//add module after jquery

//module
window.initDataTable = initDataTable;

$(function () {
    console.log('jQuery works via Vite');
    const places=[{name:"Tokyo",tz:"Asia/Tokyo"},{name:"London",tz:"Europe/London"},{name:"New York",tz:"America/New_York"},{name:"Sydney",tz:"Australia/Sydney"},{name:"Dubai",tz:"Asia/Dubai"}],p=places[Math.random()*places.length|0];console.log(`${p.name}: ${new Intl.DateTimeFormat("en-US",{timeZone:p.tz,hour:"numeric",minute:"2-digit",hour12:true}).format(new Date())}`);

});


document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("select.searchable").forEach((select) => {

        if (!select.tomselect) { // prevent duplicate init
            new TomSelect(select,{
                create: false,
                allowEmptyOption: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }

    });

});


