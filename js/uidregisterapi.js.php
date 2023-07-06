
<?php
/* Copyright (C) 2022 Amael Parreaux-Ey <amael.parreaux-ey@resilio-solutions.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Library javascript to enable Browser notifications
 */

if (!defined('NOREQUIREUSER')) {
	define('NOREQUIREUSER', '1');
}
if (!defined('NOREQUIREDB')) {
	define('NOREQUIREDB', '1');
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
if (!defined('NOREQUIRETRAN')) {
	define('NOREQUIRETRAN', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', 1);
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1);
}
if (!defined('NOLOGIN')) {
	define('NOLOGIN', 1);
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', 1);
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}


/**
 * \file    uidregisterapi/js/uidregisterapi.js.php
 * \ingroup uidregisterapi
 * \brief   JavaScript file for module UidRegisterApi.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/../main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/../main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require '../config.php';
echo "var apiToken = '{$apiToken}';";

// Define js type
header('Content-Type: application/javascript');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) {
	header('Cache-Control: max-age=3600, public, must-revalidate');
} else {
	header('Cache-Control: no-cache');
}
?>


/* Javascript library of module UidRegisterApi */
window.onload = function(e) {

    const path = window.location.pathname;
    const RESULTS_TO_SHOW = 10;
    if (path.slice(-16,) === "societe/card.php") {
    
        /* DEFINING FUNCTIONS */
        // Format CHE
        const formatCHE = function(input, tva=false) {
            return "CHE-" + input.slice(0, 3) + "." + input.slice(3, 6) + "." + input.slice(6, 9) + (tva ? " TVA" : "")
        };

        // Parse XML to companies Object
        const parseXML = function(xmlDoc, companies) {
            var organisations = xmlDoc.getElementsByTagName("uidEntitySearchResultItem");

            if (organisations.length === 0) {
                // console.log("Can't find " + target.value + " in UID register.");
            } else {
                // Parse each result
                for (var i = 0; i < organisations.length; i++) {
                    let currentCompany = organisations[i]

                    // Mapping
                    var mapping = {
                        "name": "organisationName",
                        "uid" : "uidOrganisationId",
                        "rc_number": "organisationId",
                        "street": "street",
                        "houseNumber": "houseNumber",
                        "zipCode": "swissZipCode",
                        "canton": "cantonAbbreviation",
                        "city": "town",
                        "country": "countryIdISO2",
                        "vatStatus": "vatStatus",
                        "vatEntryStatus": "vatEntryStatus",
                        "legalForm": "legalForm",
                        "score": "rating"
                    };

                    for (var j = 0; j < Object.keys(mapping).length; j++) {
                        key = Object.keys(mapping)[j];
                        try {
                            mapping[key] = currentCompany.getElementsByTagName(mapping[key])[0].firstChild.nodeValue
                        } catch {
                            // console.log(key + " doesn't exist for " + mapping.name);
                        }
                    }

                    // Manage street
                    let street = "";
                    street += mapping.street ? mapping.street : ""
                    street += " " 
                    street += mapping.houseNumber ? mapping.houseNumber : ""

                    // Manage VAT status
                    var vatStatus = false;
                    try {
                        vatStatus = mapping.vatStatus === "2" || mapping.vatEntryStatus === "1";
                    } catch {
                        vatStatus = false;
                    }
                    
                    // Get VAT number
                    try {
                        mapping.uidVat = currentCompany.getElementsByTagName("uidVat")[0].getElementsByTagName('uidOrganisationId')[0].firstChild.nodeValue;
                    } catch {
                        mapping.uidVat = null;
                    }
                    let vatNumber = (vatStatus && mapping.uidVat) ? formatCHE(mapping.uidVat, true) : "";

                    companies[mapping.name] = {
                        "uid" : mapping.uid,
                        "rc_number" : mapping.rc_number,
                        "adress" : {
                            "street": street,
                            "zipCode": mapping.zipCode,
                            "canton": mapping.canton,
                            "city": mapping.city,
                            "country": mapping.country
                        },
                        "vatStatus": vatStatus,
                        "vatNumber": vatNumber,
                        "legalForm": mapping.legalForm,
                        "score": mapping.score
                    }
                } 
            }  

            return new Promise(function (resolve, reject) {
                    resolve('XML Parsed')
                  })
        }

        const parseSirene = (jsonResponse, companies) => {
            if (!jsonResponse || !jsonResponse.unitesLegales ) {
                return;
            }
            const legalUnits = jsonResponse.unitesLegales;
            legalUnits.forEach((legalUnit) => {
            const companyName = legalUnit.periodesUniteLegale[0].denominationUniteLegale;
            const category = legalUnit.categorieEntreprise;
                companies[companyName] = {
                    name: companyName,
                    sirene: legalUnit.siren,
                    category: category,
                };
            });
            console.log(companies);
            return new Promise(function (resolve, reject) {
                resolve(companies)
            })
        }


        // Fill the form using the data collected
        const fillFormFirst = function(company) {
            // Logging
            // Fill
            fields.uid.value = formatCHE(company.uid);
            fields.rc_number.value = company.rc_number;
            fields.adress.street.value = company.adress.street;
            fields.adress.zipCode.value = company.adress.zipCode;
            fields.adress.city.value = company.adress.city;
            fields.vatStatus.value = company.vatStatus ? "1" : "0";
            fields.vatNumber.value = company.vatNumber;

            // Manage country
            let options = fields.adress.country.options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].innerText.includes(company.adress.country)) {
                    fields.adress.country.value = options[i].value;
                    document.querySelectorAll("span#select2-selectcountry_id-container")[0].innerText = options[i].innerText;
                }
            };

            document.formsoc.action.value = (localStorage.status === "update") ? "edit" : "create";
            localStorage.status = 'filling'; 
            document.formsoc.submit();
        }

        // Fill french form
        const fillFrenchForm = (company) => {

            let options = fields.adress.country.options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].innerText.includes("FR")) {
                        fields.adress.country.value = options[i].value;
                        document.querySelectorAll("span#select2-selectcountry_id-container")[0].innerText = options[i].innerText;
                    }
                };

            document.formsoc.action.value = (localStorage.status === "update") ? "edit" : "create";
            localStorage.status = 'filling'; 
            document.formsoc.submit();
        }

        const fillFrenchFormUpdate = (company) => {
            call_siret(company.sirene).then((res) => {
                const siretObject = res.etablissements[0];
                const addresse = siretObject.adresseEtablissement;
                const region = addresse.codePostalEtablissement.slice(0, 2);
                
                fields.siren.value = company.sirene;
                fields.siret.value = siretObject.siret;
                fields.adress.city.value = addresse.libelleCommuneEtablissement;
                fields.adress.street.value = addresse.numeroVoieEtablissement + ' ' + addresse.typeVoieEtablissement + ' ' + addresse.libelleVoieEtablissement;
                
                let options = fields.adress.canton.options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].innerText.includes(region)) {
                        fields.adress.canton.value = options[i].value;
                        document.querySelectorAll("span#select2-state_id-container")[0].innerText = options[i].innerText;
                    }
                };

            })
        }

        // Fill the form after page reload
        const fillOnUpdatedForm = function(company) {
            // Manage canton
            if (company.adress.canton) {
                let options = fields.adress.canton.options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].innerText.includes(company.adress.canton)) {
                        fields.adress.canton.value = options[i].value;
                        document.querySelectorAll("span#select2-state_id-container")[0].innerText = options[i].innerText;
                    }
                };
            }

            // Manage company legal form
            if (company.legalForm) {
                const legalForms = {
                    '0109': '608', // Association
                    '0110': '609', // Fondation,
                    '0101': '600', // Raison individuelle
                    '0302': '601', // Société simple
                    '0106': '604', // SA
                    '0108': '607', // SCOP
                    '0104': '603', // Société en commandite
                    '0105': '605', // Soc. commandite par actions
                    '0103': '602', // Soc. nom collectif
                    '0107': '606' // SARL
                }
                if (company.legalForm in legalForms) {
                    fields.legalForm.value = legalForms[company.legalForm];
                    let options = fields.legalForm.options;
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value === fields.legalForm.value) {
                            document.querySelectorAll("span#select2-forme_juridique_code-container")[0].innerText = options[i].innerText;
                        }
                    };
                }
            }
            // Clear localStorage
            localStorage.clear();
        }

        // Ensure the page in fully loaded when JS 
        const showResult = function(target) {
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            if (!target.value) { return false;}
                currentFocus = -1;

            if (Object.keys(companies).length === 0) {
                if (document.getElementById("UID-fail-notice")) {
                    document.getElementById("UID-fail-notice").innerHTML = "<div style={display: inline-block; margin-left: 15px;}>Can't find <strong>" + target.value + "</strong> in UID register.</div>";
                } else {
                    let uidFailNotice = document.createElement('td');
                    uidFailNotice.innerHTML = "<div style={display: inline-block; margin-left: 15px;}>Can't find <strong>" + target.value + "</strong> in UID register.</div>";
                    uidFailNotice.id = "UID-fail-notice";
                    target.parentNode.parentNode.appendChild(uidFailNotice);
                }
                return false;
            }

            if (!target.value) { return false;}
            currentFocus = -1;

            if (Object.keys(companies).length === 0) {
                if (document.getElementById("UID-fail-notice")) {
                    document.getElementById("UID-fail-notice").innerHTML = "<div style={display: inline-block; margin-left: 15px;}>Can't find <strong>" + target.value + "</strong> in UID register.</div>";
                } else {
                    let uidFailNotice = document.createElement('td');
                    uidFailNotice.innerHTML = "<div style={display: inline-block; margin-left: 15px;}>Can't find <strong>" + target.value + "</strong> in UID register.</div>";
                    uidFailNotice.id = "UID-fail-notice";
                    target.parentNode.parentNode.appendChild(uidFailNotice);
                }
                return false;
            }

            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", target.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");

            /*append the DIV element as a child of the autocomplete container:*/
            target.parentNode.appendChild(a);

            /* Get scores and sort them -- USELESS
            var scores = Object.keys(companies).map(name => {return companies[name].score});
            scores = scores.sort((a, b) => {return parseInt(b) - parseInt(a)});
            scores = scores.slice(0, RESULTS_TO_SHOW);
            */

            /*for each item in the array...*/
            for (i = 0; i < Object.keys(companies).length; i++) {
                let name = Object.keys(companies)[i];
                
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*Fill with name and UID number*/
                b.innerHTML = name + " - " + formatCHE(companies[name]["uid"]);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + name + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                      /*insert the value for the autocomplete text field:*/
                      target.value = this.getElementsByTagName("input")[0].value;
                      fillFormFirst(companies[name]);
                      /*close the list of autocompleted values,
                      (or any other open lists of autocompleted values:*/
                      closeAllLists();
                      deleteLoading();
                  });
                a.appendChild(b);

                //}
            }
        };

        const showSireneResult = (companies) => {
            if (!target.value)
                return false;
            currentFocus = -1;
            a = document.createElement("DIV");
            a.setAttribute("id", target.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            target.parentNode.appendChild(a);

            for (i = 0; i < Object.keys(companies).length; i++) {
                let name = Object.keys(companies)[i];
                b = document.createElement("DIV");
                b.innerHTML = `${companies[name].sirene} - ${name}`;
                b.innerHTML += "<input type='hidden' value='" + name + "'>";
                b.addEventListener("click", function(e) {
                    target.value = this.getElementsByTagName("input")[0].value;
                    
                    
                    fillFrenchForm(companies[name]);
                    closeAllLists();
                    deleteLoading();
                });
                a.appendChild(b);
            }
        }

        function showUpdate(target) {
            // If exact match : check diff and propose update
            if (target.value in companies) {
                // The current name is exactly one on the list returned from API, we just ceck if something changed.
                let company = companies[target.value];
                let to_update = [];

                // Fill to_update with the fields that can be updated
                if (!(fields.uid.value === formatCHE(company.uid))) {
                    to_update.push('UID');
                }
                if (!(fields.rc_number.value === company.rc_number)) {
                    to_update.push('RC number');
                }
                if (!(fields.adress.street.value === company.adress.street)) {
                    to_update.push('Street');
                }
                if (!(fields.adress.zipCode.value === company.adress.zipCode)) {
                    to_update.push('Zip code');
                }
                if (!(fields.adress.city.value === company.adress.city)) {
                    to_update.push('Town');
                }
                if (!(fields.vatStatus.value === (company.vatStatus ? "1" : "0"))) {
                    to_update.push('VAT Status');
                }
                if (!(fields.vatNumber.value === company.vatNumber)) {
                    to_update.push('VAT Number');
                }

                if (to_update.length > 0) {
                    // Add component to show update
                    let updateNotice = document.createElement('td');
                    updateNotice.innerHTML = "<div style={display: inline-block; margin-left: 15px;}><strong>Update possible: </strong>" + String(to_update) + "</div>";
                    updateNotice.id = "update-notice";
                    target.parentNode.parentNode.appendChild(updateNotice);

                    // Add component to start update
                    let updateBtn = document.createElement('button')
                    updateBtn.classList.add("button");
                    updateBtn.type = "button";
                    updateBtn.id = "update-UIDreg";
                    updateBtn.innerText = "Update all";
                    document.addEventListener("click", function (e) {
                        e.preventDefault();
                        fillFormFirst(company);
                    });
                    target.parentNode.parentNode.appendChild(updateBtn);
                }

            } else {    // else, print new name
                showResult(target);
            }
        }

        function addActive(x) {
            /*a function to classify an item as "active":*/
            if (!x) return false;
            /*start by removing the "active" class on all items:*/
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            /*add class "autocomplete-active":*/
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            /*a function to remove the "active" class from all autocomplete items:*/
            for (var i = 0; i < x.length; i++) {
              x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt) {
            /*close all autocomplete lists in the document,
            except the one passed as an argument:*/
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
              if (elmnt != x[i] && elmnt != target) {
                x[i].parentNode.removeChild(x[i]);
            }
          }
        }

        function deleteLoading() {
            try {
                document.getElementById("loading-UIDreg").remove();
            } catch {
                return false;
            }
            return true;
        }

        function deleteFailNotice() {
            try {
                document.getElementById("UID-fail-notice").remove();
            } catch {
                return false;
            }
            return true;
        }

        // Define SOAP API-calling function
        const call_soap = function(query, callback_response) {

            // Abort if too short entry
            if (query.length > 3) {
                // Prepare request
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.open('POST', 'https://www.uid-wse.admin.ch/V5.0/PublicServices.svc', true);

                // Build SOAP request
                var sr =
                    '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:uid="http://www.uid.admin.ch/xmlns/uid-wse" xmlns:ns="http://www.uid.admin.ch/xmlns/uid-wse/5" xmlns:ns1="http://www.ech.ch/xmlns/eCH-0097/5" xmlns:ns2="http://www.uid.admin.ch/xmlns/uid-wse-shared/2">' +
                      '<soapenv:Header/>' +
                      '<soapenv:Body>' +
                         '<uid:Search>' +
                            '<uid:searchParameters>' +
                               '<ns:uidEntitySearchParameters>' +
                                  '<ns:organisationName>' + query + '</ns:organisationName>' +
                               '</ns:uidEntitySearchParameters>' +
                            '</uid:searchParameters>' +
                            '<uid:config>' +
                               '<ns2:maxNumberOfRecords>' + RESULTS_TO_SHOW + '</ns2:maxNumberOfRecords>' +
                            '</uid:config>' +
                         '</uid:Search>' +
                      '</soapenv:Body>' +
                   '</soapenv:Envelope>';

                // Define response-handling function
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == 4) {
                        if (xmlhttp.status == 200) {
                            // Delete loading info
                            deleteLoading();

                            // Parse response
                            if (window.DOMParser)
                            {
                                parser = new DOMParser();
                                xmlDoc = parser.parseFromString(xmlhttp.responseText, "text/xml");
                            }
                            else // Internet Explorer
                            {
                                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                                xmlDoc.async = false;
                                xmlDoc.loadXML(xmlhttp.responseText);
                            }
                            companies = {};
                            callback_response(xmlDoc);
                            
                        }
                    }
                }

                // Send the POST request
                xmlhttp.setRequestHeader('Content-Type', 'text/xml');
                xmlhttp.setRequestHeader('SOAPAction', 'http://www.uid.admin.ch/xmlns/uid-wse/IPublicServices/Search');
                xmlhttp.send(sr);
                deleteFailNotice();

                // Mark loading
                if (document.getElementById("loading-UIDreg") === null) {
                    let loading = document.createElement('td');
                    loading.innerHTML = "Loading...";
                    loading.id = "loading-UIDreg";
                    target.parentNode.parentNode.appendChild(loading);
                }
            }
        };

        // Define SIRENE API-calling function
        const call_sirene  = function(query) {
            return new Promise((resolve, reject) => {
                // Abort if too short entry
                if (query.length < 4)
                    reject(new Error('Query too short'));

                // Prepare request
                const xmlhttp = new XMLHttpRequest();
                const token = apiToken
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const currentDate = year + '-' + month + '-' + day;
                // remove white spaces from query
                // query = query.replace(/\s/g, '');
                var SirenUrl = 'https://api.insee.fr/entreprises/sirene/V3/siren?q=periode(denominationUniteLegale%3A' + query + ')&date=' + currentDate + '&nombre=' + RESULTS_TO_SHOW;

                xmlhttp.open('GET', SirenUrl, true);
                xmlhttp.setRequestHeader('Accept', 'application/json');
                xmlhttp.setRequestHeader('Authorization', 'Bearer ' + token);
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState === 4) {
                        if (xmlhttp.status === 200){
                            var jsonResponse = JSON.parse(xmlhttp.responseText);
                            deleteLoading();   
                            companies = {}
                            resolve(jsonResponse);
                        }
                        else {
                            deleteLoading();
                            // mark name not found if status is 404
                            if (xmlhttp.status === 404 && document.getElementById("UID-fail-notice") === null) {
                                let failNotice = document.createElement('td');
                                failNotice.innerHTML = `No company found for <strong>${query}</strong>`;
                                failNotice.id = "UID-fail-notice";
                                target.parentNode.parentNode.appendChild(failNotice);
                            }
                            reject(new Error('Request failed with status ' + xmlhttp.status));
                        }
                    }
                };

                xmlhttp.send();

                // Mark loading
                if (document.getElementById("loading-UIDreg") === null) {
                    let loading = document.createElement('td');
                    loading.innerHTML = "Loading...";
                    loading.id = "loading-UIDreg";
                    target.parentNode.parentNode.appendChild(loading);
                }
            });
        };

        const call_siret = (sirenNbr) => {
            return new Promise((resolve, reject) => {
                console.log('here: ',sirenNbr);
                const token = apiToken;
                const siretUrl = 'https://api.insee.fr/entreprises/sirene/V3/siret?q=siren:' + sirenNbr;
                const xmlhttp = new XMLHttpRequest();
                xmlhttp.open('GET', siretUrl, true);
                xmlhttp.setRequestHeader('Accept', 'application/json');
                xmlhttp.setRequestHeader('Authorization', 'Bearer ' + token);
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState === 4) {
                        if (xmlhttp.status === 200){
                            var jsonResponse = JSON.parse(xmlhttp.responseText);
                            resolve(jsonResponse);
                        } else {
                            reject(new Error('Request failed with status ' + xmlhttp.status));
                        }
                    }
                };
                xmlhttp.send();
            })
        } 
        
        /* VARIABLES DEFINITIONS */
        var currentFocus;
        // Get target
        const target = document.querySelectorAll("input#name")[0];
        // create a country button after the target that will switch the country from "CH" to "FR"
        const countryBtn = document.createElement("button");
        countryBtn.id = "countryBtn";
        target.parentNode.appendChild(countryBtn);
        let currentCountry = localStorage.getItem("currentCountry") || "CH";
        var companies = {};
        countryBtn.innerText = currentCountry;

        // switch country on click
        countryBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (countryBtn.innerText == "CH") {
                countryBtn.innerText = "FR";
                currentCountry = "FR";
                // Manage country
                let options = fields.adress.country.options;
                for (var i = 0; i < options.length; i++) {
                    if (options[i].innerText.includes("FR")) {
                        fields.adress.country.value = options[i].value;
                        document.querySelectorAll("span#select2-selectcountry_id-container")[0].innerText = options[i].innerText;
                    }
                };
            } else {
                countryBtn.innerText = "CH";
                currentCountry = "CH";
            }
            localStorage.setItem("currentCountry", currentCountry);
        });

        
        // Get fields
        let fields = {
            "uid" : document.querySelectorAll("input#idprof1")[0],
            "siren" : document.querySelectorAll("input#idprof1")[0],
            "siret" : document.querySelectorAll("input#idprof2")[0],
            "rc_number" : document.querySelectorAll("input#idprof4")[0],
            "adress" : {
                "street": document.querySelectorAll("textarea#address")[0],
                "zipCode": document.querySelectorAll("input#zipcode")[0],
                "canton": document.querySelectorAll("select#state_id")[0],
                "city": document.querySelectorAll("input#town")[0],
                "country": document.querySelectorAll("select#selectcountry_id")[0]
            },
            "vatStatus": document.querySelectorAll("select#assujtva_value")[0],
            "vatNumber": document.querySelectorAll("input#intra_vat")[0],
            "legalForm": document.querySelectorAll("select#forme_juridique_code")[0]
        }

        /* MAIN CODE */
        // If target is undefined, we are not on the form page
        if (target === undefined) {return false}

        // Set CSS properly
        target.parentNode.classList.add('autocomplete');
        target.setAttribute("autocomplete", "off");


        // Finish info update if page reloaded
        /* 3 cases :
        + New third party
        + Automatic update
        + Manual update
        */
        if (target.value.length > 0) {
            // Not a new third party
            if (localStorage.status === 'filling') {
                // Page just reloaded and country is adapted
                if (currentCountry == "CH") {
                    call_soap(
                        target.value, 
                        xmlDoc => parseXML(xmlDoc, companies)
                        .then(
                            fillOnUpdatedForm(companies[target.value])
                        )
                    );
                } else {
                    deleteFailNotice(); 
                    console.log('here: ',companies, target.value);
                    call_sirene(target.value).then((res) => {
                        parseSirene(res, companies)
                            .then(fillFrenchFormUpdate(companies[target.value]))
                    }).catch((err) => {
                        console.log('err: ',err);
                    });
                }
            } else {
                // Manual update of the third party
                localStorage.status = "update";
                call_soap(
                    target.value, 
                    xmlDoc => parseXML(xmlDoc, companies)
                        .then(
                            showUpdate(target)
                            )
                );
            }
        }

        // Add event listener
        target.addEventListener("input", function(e) {
            if (currentCountry == "CH") {
                call_soap(
                    target.value, 
                    xmlDoc => parseXML(xmlDoc, companies)
                        .then(showResult(target))
                    );
            } else if (currentCountry == "FR") {
                deleteFailNotice(); 
                console.log('mtn');
                call_sirene(target.value).then((res) => {
                    parseSirene(res, companies)
                        .then(showSireneResult(companies))
                }).catch((err) => {
                    console.log('err: ',err);
                });
            }
        });

        /*execute a function presses a key on the keyboard:*/
        target.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
              /*and simulate a click on the "active" item:*/
              if (x) x[currentFocus].click();
            }
          } else if (e.key == "Backspace") {
            // On backspace, clean the list
            closeAllLists();
          }
        });

        /*execute a function when someone clicks in the document:*/
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
    };
};
