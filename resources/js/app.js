//import './bootstrap';

/*
import 'bootstrap';          // Bootstrap 5.2.3 JS
import $ from 'jquery';

window.$ = window.jQuery = $;


*/

import 'bootstrap';
import $ from 'jquery';

//jquerry
window.$ = window.jQuery = $;

// import the module but don't auto-run
import { initDataTable } from './datatable';
//add module after jquery

//module
window.initDataTable = initDataTable;

$(function () {
    console.log('jQuery works via Vite');
    const places=[{name:"Tokyo",tz:"Asia/Tokyo"},{name:"London",tz:"Europe/London"},{name:"New York",tz:"America/New_York"},{name:"Sydney",tz:"Australia/Sydney"},{name:"Dubai",tz:"Asia/Dubai"}],p=places[Math.random()*places.length|0];console.log(`${p.name}: ${new Intl.DateTimeFormat("en-US",{timeZone:p.tz,hour:"numeric",minute:"2-digit",hour12:true}).format(new Date())}`);

});

