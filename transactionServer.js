const moment = require('moment-timezone');
const mysql = require('mysql');
const login = require('./login.json')
const spreadsheetLocation = require('./spreadsheet-location.json')
const ExcelJS = require('exceljs');

const connection = mysql.createConnection({
  host     : login.host,
  user     : login.user,
  password : login.password,
  database : login.database
});



const workbook = new ExcelJS.Workbook();

// Load an existing workbook
workbook.xlsx.readFile(spreadsheetLocation.location)
    .then(() => {
        // Work with the workbook
        // const worksheet = workbook.getWorksheet('Sheet1');
        const worksheet = workbook.worksheets[0];
        //the loop starts on 2 to avoid trying to process the header row
        let processedRows = 0;
        console.log(`${worksheet.rowCount} is the row count`);
        for (let i = 2; i < worksheet.rowCount+1; i++) { 
            let rowArray = []
            const row = worksheet.getRow(i);
            row.eachCell({ includeEmpty: true }, function(cell, colNumber) {
                rowArray.push(cell.value);    
            });

            if(typeof rowArray[2] === 'object') {
                rowArray[2] = rowArray[2]['result'];
            if( typeof rowArray[2] === 'undefined'){
                rowArray[2] = 0;
            }
            };
            let duplicateCheckQuery = 'SELECT * FROM transactions WHERE id = ? AND cost = ?';
            connection.query(duplicateCheckQuery, [rowArray[0], rowArray[2]], function(error, results, fields) {
                if (error) throw error;
                if (results.length> 0) {
                    //results found
                    
                    if(processedRows == worksheet.rowCount-1){ 
                        connection.end();
                        process.exit(0);
                    } 
                    processedRows++;
                } else {
                    //no results found
                    let insertStatement = 'insert into transactions (id, item, cost, category, payment_method, date) values(?, ?, ?, ?, ?, ?)';
                    console.log(`adding ${rowArray[0]}`)
                    connection.query(insertStatement, [rowArray[0], rowArray[1], rowArray[2], rowArray[3], rowArray[4], rowArray[5]], function(error, results, fields) {
                        if (error) throw error;    
                    processedRows++;
                    if(processedRows == worksheet.rowCount-1){ 
                        connection.end()
                        process.exit(0)
                    
                        } 
                    });
                    
                                        
                }
    
            }); 
        }
    })
    .catch(error => {
        console.log('Error:', error);
        process.exit(1); // Exit with an error code
    });

