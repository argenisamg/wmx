/** GLOBALS **/
//IMPORTS
//Forms
let adminForm = document.getElementById('adminForm');
let inoutForm = document.getElementById('inoutForm');
let loanForm = document.getElementById('loanForm');
//Inputs
let articleadmin = document.getElementById('article_id');
let articleinout = document.getElementById('articleinout');
let id_cable = document.getElementById('id_cable');
let selectSerialnumber = document.getElementById('serialnumber2');
let quantity_prestamo = document.getElementById('quantity_prestamo');
let picsnpn = document.getElementById('picsnpn');

//Labels
let labelSN2 = document.getElementById('labelSN2');
//Tables
// let dataBodyPage2 = document.getElementById('dataBodyPage2');
//MODAL
let elements_modal = document.getElementById('elements-modal');

//Arrays
// let arrQuantityBorrowed = [];
//Variables
let picParamIs = "";

//RENDER
const renderFunction = (paramHtml, paramInnerHtml) => paramInnerHtml.innerHTML = paramHtml;
//Block And None components
const blockNoneFunc = (parmBlNo, paramComponent) => paramComponent.style.display = parmBlNo;

/**
 * 
 * Swal functions Messages:
 */
const mostrarMensajeSinRegistros = () => {
    Swal.fire({
        title: 'Attention!',
        text: 'There is not items for this action.',
        icon: 'warning',
        confirmButtonText: 'OK'
    });
};


const selectPartNumber = async (selectParam) => {    
    let selectedIs = selectParam.value;
    let arrdataGet = [];
    arrdataGet = await dataGetByParam("../phpamg/GetPartNumber.php", selectedIs);
    if (arrdataGet.length === 0) {
        Swal.fire({
            title: 'Attention!',
            text: 'There is no items for this action.',
            icon: 'warning',
            confirmButtonText: 'OK',
        });
        return;
    }
    let stringAlgo = `<option value="">-- Select article --</option>`;
    
        arrdataGet.forEach((item) => {
            stringAlgo += (item.sn === "yes") ? `<option value="${item.partnumber}">${item.partnumber}</option>` : `<option value="${item.id}">${item.partnumber}</option>`;            
        });        
        renderFunction(stringAlgo, id_cable);        
};

//Click event to each button 'Return Item'
const clickButtonReturnItem = () => {    
    let returnbtn = document.querySelectorAll('#returnbtn');
    let numberQantityBorrowed = document.querySelectorAll('#numberQantityBorrowed');
    returnbtn.forEach((btnItem, index) => {
        btnItem.addEventListener('click', (amg) => {   
            let atribs = amg.target.attributes;
            let idcableParam = atribs.idcable.value;
            let idborrowedParam = atribs.idborrowed.value;
            let quantityoutputParam = atribs.quantityData.value;
            let valueQuantity = numberQantityBorrowed[index].value;            
            // console.log(idcableParam, idborrowedParam, quantityoutputParam, valueQuantity);                 
            clickEvent(idcableParam, idborrowedParam, quantityoutputParam, valueQuantity);            
        });
    });
};

const buttonDetailsClick = () => {    
    let actiondetails = document.querySelectorAll('#actiondetails');    
    actiondetails.forEach((button) => {
        button.addEventListener('click', (atr) => {   
            let atribs = atr.target.attributes;
            let articleis = atribs.articleis.value;
            // console.log(articleis); 
            getDetailsFunc(articleis);           
                 
        });
    });
};

const getDetailsFunc = async (articleis) => {
    let tableModal = "";
    let getDetails = await dataGetByParam("../phpamg/GetDetails.php", articleis);    
    tableModal = `				
						<table id="tableDetails" class="table-amg textcenter">
							<thead id="dataHead">
								<th>#</th>
								<th>PN</th>
								<th>CATEGORY</th>
								<th>PIC</th>								
								<th>QUANTITY</th>								
							</thead>
							<tbody id="tbodyDetails" class="text-dark-amg">`;
                            getDetails.forEach((details) => {
                                tableModal += `<tr>
                                                    <td class="padding-ten">${details.rows}</td>
                                                    <td class="padding-ten">${details.partnumberDetail}</td>
                                                    <td class="padding-ten">${details.categoryDetail}</td>
                                                    <td class="padding-ten">${details.personDetail}</td>
                                                    <td class="padding-ten">${details.quantityDetail}</td>
                                                </tr>`;
                            });
    tableModal +=           `</tbody>
                        </table>`;
    
    renderFunction(tableModal, elements_modal);

    $("#modal-details").modal("show");
};

const clickEvent = (idcableParam, idborrowedParam, quantityoutputParam, valueQuantity) => {          
    let resCalculo = quantityoutputParam - valueQuantity;
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
            // let arrayAttributesTr = param.target.attributes;           
            //let quantityoutput = quantityoutputParam;
            // let idCable = arrayAttributesTr.idcable.value;
            // let idParam = arrayAttributesTr.idborrowed.value;
            // let quantityoutput = arrayAttributesTr.quantityoutput.value;
            let dataObj = {
                "idcable": null, 
                "id_prestamo": null, 
                "borrowed_quantity": null,
                "return_quantity": null,
                "calculo": null,
            }; 
            dataObj.idcable = idcableParam;
            dataObj.id_prestamo = idborrowedParam;
            dataObj.borrowed_quantity = quantityoutputParam;
            dataObj.return_quantity = valueQuantity;
            dataObj.calculo = resCalculo;
            let json = JSON.stringify(dataObj, null, 2);
            //console.log(json);
            sendData(json, "../phpamg/UpdateReturnMaterial.php");
            dataTableBorrowed.ajax.reload(function () {
               clickButtonReturnItem();
            });             
        } // end if
    })                         
    // consultByPic(picsnpn); 
}; // end clickEvent

let dataTableBorrowed;
const dataTableFuncBorrowed = () => {    
    $(document).ready(function () {                
        if (!$.fn.DataTable.isDataTable('#tableBorrowed')) {
            dataTableBorrowed = $('#tableBorrowed').DataTable({
                ajax: {
                    url: `../phpamg/SearchAPI.php?param=${picParamIs}`,
                    type: 'GET',
                    dataType: 'json',
                    dataSrc: function(response) {
                        if (response && Array.isArray(response) && response.length > 0) {
                            return response;
                        } else {
                            mostrarMensajeSinRegistros();                            
                            return [];
                        }
                    },
                },
                columns: [                   
                    { data: 'rows' },
                    { data: 'pic' },
                    { data: 'article' },                   
                    { data: 'pn' },
                    { data: 'sn' },
                    { data: 'dateout' },
                    { data: 'datein' },
                    { data: 'quantityoutput' },
                    { data: 'actions' },
                ],
                responsive: true,
                //destroy: true,
                iDisplayLength: 10,
                initComplete: function () {       
                    clickButtonReturnItem();
                    // $('#tableBorrowed tbody').on('click', '#returnbtn', function () {
                    //     let idcable = $(this).attr('idcable');
                    //     let idborrowed = $(this).attr('idborrowed');                       
                    //     let quantityData = $(this).closest('tr').find('input[name="numberQantityBorrowed"]').attr('quantityData');
                    //     let valueQuantity = $(this).closest('tr').find('input[name="numberQantityBorrowed"]').val();        
                    //     clickEvent(idcable, idborrowed, quantityData, valueQuantity);                       
                    // });
                } // end initComplete
            }); // end dataTable
        } // end if
    });    

    if (dataTableBorrowed) {
        dataTableBorrowed.ajax.url(`../phpamg/SearchAPI.php?param=${picParamIs}`).load();        
    }

}; // end dataTableFuncBorrowed

// const consultByPic = async (paramSend) => {    
//     let arrdataGet = [];
//     let stringAlgo = "";
    
//     arrdataGet = await dataGetByParam("../phpamg/SearchAPI.php", paramSend);
//     dataTableFuncBorrowed(paramSend);
//     if (arrdataGet.length === 0) {
//         arrdataGet = [];
//         stringAlgo = "";
//         Swal.fire({
//             title: 'Attention!',
//             text: 'There is not items for this action.',
//             icon: 'warning',
//             confirmButtonText: 'OK'
//         });
//         renderFunction("", dataBodyPage2);        
//     }  else {
//         arrdataGet.forEach((item) => {        
//             stringAlgo += `<tr>
//                                 <td>${item.rows}</td>                            
//                                 <td>${item.pic}</td>
//                                 <td>${item.article}</td>
//                                 <td>${item.pn}</td>
//                                 <td>${item.sn}</td>
//                                 <td>${item.dateout}</td>
//                                 <td>${item.datein}</td>
//                                 <td>${item.quantityoutput}</td>                           
//                                 <td>${item.actions}</td>                           
//                             </tr>`;
//             arrQuantityBorrowed[arrQuantityBorrowed.length] = {
//                 "idcable": item.idcable,  
//                 "id": item.id,  
//                 "quantity": item.quantityoutput,  
//             };
            
//         });    
//         renderFunction(stringAlgo, dataBodyPage2);
//     }        
//     arrdataGet = [];
//     stringAlgo = "";            
// };

const selectSerialNumber = async (selectParam) => {    
    let selectedIs = selectParam.value;    
    let arrdataGet = [];
    arrdataGet = await dataGetByParam("../phpamg/GetSerialNumber.php", selectedIs);    
    if (arrdataGet.length > 0 && arrdataGet[0].serialnumber !== "") {
        blockNoneFunc("block", selectSerialnumber);        
        blockNoneFunc("block", labelSN2);        
        let stringAlgo = `<option value="">-- Select article --</option>`;
            arrdataGet.forEach((item) => {            
                stringAlgo += `<option value="${item.id}">${item.serialnumber}</option>`;
            });
            renderFunction(stringAlgo, selectSerialnumber);        
        // Swal.fire({
        //     title: 'Attention!',
        //     text: 'There is no items for this action.',
        //     icon: 'warning',
        //     confirmButtonText: 'OK'
        // });
        // return;
    } else {
        blockNoneFunc("none", selectSerialnumber);        
        blockNoneFunc("none", labelSN2);        
    }
};

const selectAdministration = async () => {
    try {
        let arrdataGet = await dataGet("../phpamg/GetArticle.php");
        let stringAlgo = "";

        stringAlgo = `<option value="">-- Select article --</option>`;
        arrdataGet.forEach((item) => {
            stringAlgo += `<option value="${item.id}">${item.article}</option>`;
        });
        renderFunction(stringAlgo, articleadmin);        
        renderFunction(stringAlgo, articleinout);        
    } catch (error) {
        console.error(error);
    }
}; // end selectAdministration

// let dataTable;
// const selectInfo = () => {    
//     $(document).ready(function () {
//         if (!$.fn.DataTable.isDataTable('#dataCable')) {
//             dataTable = $('#dataCable').DataTable({
//                 ajax: {
//                     url: '../phpamg/GetCableInfo.php',
//                     type: 'GET',
//                     dataType: 'json',
//                     dataSrc: function(response) {
//                         if (response && Array.isArray(response) && response.length > 0) {
//                             return response;
//                         } else {
//                             mostrarMensajeSinRegistros();
//                         }
//                     },
//                 },
//                 columns: [
//                     { data: 'rows' },
//                     { data: 'article' },
//                     { data: 'partnumber' },
//                     // {
//                     //     data: 'evidence',
//                     //     render: function(data, type, row, meta) {
//                     //         return `<img src="${data}" class="element-clic" onClick="showImage('${row.qr}', '${row.evidence}', '${row.description}');" width="50px" height="50px" alt="Not available" >`;
//                     //     }
//                     // },
//                     { data: 'serialnumber' },
//                     { data: 'registerdate' },
//                     { data: 'descriptioncable' },
//                     { data: 'quantity' },
//                     { data: 'borrowed' }
//                 ],
//                 responsive: true,
//                 destroy: true,
//                 iDisplayLength: 10
//             }); // end dataTable
//         } // end if
//     });
    
// }; // end selectInfo

const dataGetByParam = async (endPoint, param) => {
    try {
        let url = endPoint;   
        let formData = new FormData(); 
        formData.append("param", param);
        const response = await fetch(url, {
            method: "POST",
            body: formData        
        });

        if (!response.ok) {
            throw new Error('An error occurred while submitting the request.');
        }

        const data = await response.json();

        if (data.status) {
            return data.data;
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error while getting data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            adminForm.reset();
            return null;
        }
    } catch (error) {
        console.error(error);        
        return null;
    }
}; // end dataGetByParam

const dataGet = async (endPoint) => {
    try {
        let url = endPoint;    
        const response = await fetch(url, {
            method: "GET"        
        });

        if (!response.ok) {
            throw new Error('An error occurred while submitting the request.');
        }

        const data = await response.json();

        if (data.status) {
            return data.data;
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error while getting data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            adminForm.reset();
            return null;
        }
    } catch (error) {
        console.error(error);
        // Manejar el error de alguna manera apropiada
        return null;
    }
}; // end dataGet

const sendData = (jsonParam, endPoint) => {    
    let dataForm = new FormData();
    dataForm.append('datasend', jsonParam);    
    fetch(endPoint, {
        method: "POST",
        body: dataForm
    })
    .then(response => {
        if (response.ok) 
            return response.json();
        else 
            throw new Error('An error occurred while submitting the request.');        
    })
    .then(data => {
        let msg = "";
        if (data.status) {
            msg = data.msg;
            Swal.fire({
				title: 'Success!',
				text: `Server says: ${msg}`,
				icon: 'success',
				confirmButtonText: 'OK'
			});
            adminForm.reset();
            // loanForm.reset();
            inoutForm.reset();
            dataTableInventory.ajax.reload();
        } else {
            Swal.fire({
				title: 'Error!',
				text: `Server says: ${msg}`,
				icon: 'error',
				confirmButtonText: 'OK'
			});
            adminForm.reset();
        }
    })
}; //end sendData

/**
 * Data Tables
 */
let dataTableInventory;
const dataTableFuncInventory = () => {    
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#dataCable')) {
            dataTableInventory = $('#dataCable').DataTable({
                ajax: {
                    url: '../phpamg/GetCableInfo.php',
                    type: 'GET',
                    dataType: 'json',
                    dataSrc: function(response) {
                        if (response && Array.isArray(response) && response.length > 0) {                                                        
                            return response;
                        } else {
                            mostrarMensajeSinRegistros();
                        }
                    },
                },
                columns: [
                    { data: 'rows' },
                    { data: 'article' },
                    { data: 'partnumber' },                   
                    { data: 'serialnumber' },
                    { data: 'registerdate' },
                    { data: 'descriptioncable' },
                    { data: 'quantity' },
                    { data: 'borrowed' },
                    { data: 'actions' },
                ],
                responsive: true,
                destroy: true,
                iDisplayLength: 10,
                initComplete: function () {       
                    buttonDetailsClick();                    
                } // end initComplete
            }); // end dataTable
        } // end if
    });    
}; // end selectInfo

document.addEventListener('DOMContentLoaded', function () {       
    selectAdministration();
    dataTableFuncInventory();
    // selectInfo(); 
    adminForm.onsubmit = (e) => {
        e.preventDefault();        
        let fData = new FormData();        
        for (let item of adminForm.elements) {            
            if (item.name !== "resetform" && item.name !== "savenewregister") {     
                fData.append(item.name, item.value);                
            }
        } // end for
        fData.append("borrowed_quantity", 0);                
        let json = JSON.stringify(Object.fromEntries(fData.entries()), null, 2);        
        //console.log(json);
        sendData(json, "../phpamg/InsertDataToDb.php");
        adminForm.reset();       
        dataTableInventory.ajax.reload(); 
    }// end onsubmit
    
    inoutForm.onsubmit = (e) => {
        e.preventDefault();        
        let fData = new FormData();
        if ((labelSN2.style.display === "block" && quantity_prestamo.value > 1) || (quantity_prestamo.value <= 0 || quantity_prestamo.value == "")) {
            Swal.fire({
				title: 'Error!',
				text: "Output Quantity must be 1.",
				icon: 'error',
				confirmButtonText: 'OK'
			});
            return;
        }
        for (let item of inoutForm.elements) {                       
            if (item.name !== "resetform2" && item.name !== "saveoperation") { 
                if (item.name !== "articleinout" && item.name) {  
                    if (labelSN2.style.display === "none" && item.name === "serialnumber2") {
                        continue;
                    }                   
                    fData.append(item.name, item.value);                
                }    
            }
        } // end for
        fData.append("borrowed", "false");
        let json = JSON.stringify(Object.fromEntries(fData.entries()), null, 2);        
        console.log(json);
        sendData(json, "../phpamg/InsertDataInOut.php");
        inoutForm.reset();       
        dataTableInventory.ajax.reload();        
    }// end onsubmit

    loanForm.onsubmit = (e) => {
        e.preventDefault();        
        if (picsnpn.value === "") {
            Swal.fire({
				title: 'Error!',
				text: "Empty data to send.",
				icon: 'error',
				confirmButtonText: 'OK'
			});
            return;
        }              
        picParamIs = picsnpn.value;        
        dataTableFuncBorrowed();                                                 
        // consultByPic(picsnpn.value);        
    }// end onsubmit
}); // end DOMContentLoaded
