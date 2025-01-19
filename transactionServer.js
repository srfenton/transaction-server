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
        console.log(`${worksheet.rowCount} is the row count`);

        function processRow(i) {
            let rowArray = [];
            const row = worksheet.getRow(i);
            row.eachCell({ includeEmpty: true }, function(cell, colNumber) {
                rowArray.push(cell.value);
            });
            if(typeof rowArray[2] === 'object') {
                rowArray[2] = rowArray[2]['result'];
            if( typeof rowArray[2] === 'undefined'){
                rowArray[2] = 0;
            }
            if(rowArray[1] === 'undefined'){
                console.log('exiting due to empty item value');
                process.exit(0);
            }
            };

            let date = rowArray[5];
            let formattedDate = moment.utc(date).format('YYYY-MM-DD'); // Format to YYYY-MM-DD
            let duplicateCheckQuery = 'SELECT * FROM transactions WHERE item = ? AND cost = ? AND category = ? AND payment_method = ? AND date = ?';
            connection.query(duplicateCheckQuery, [rowArray[1], rowArray[2], rowArray[3], rowArray[4], formattedDate], function(error, results, fields) {
                if (error) throw error;
                if (results.length > 0) {
                    if (i === worksheet.rowCount) {
                        connection.end();
                        process.exit(0);
                    }
                } else {
                    let insertStatement = 'INSERT INTO transactions (item, cost, category, payment_method, date, notes) VALUES(?, ?, ?, ?, ?, ?)';
                    connection.query(insertStatement, [rowArray[1], rowArray[2], rowArray[3], rowArray[4], formattedDate, rowArray[6]], function(error, results, fields) {
                        if (error) throw error;
                        console.log(`row insterted for ${rowArray[1]}`)
                        if (i === worksheet.rowCount-1) {
                            connection.end();
                            console.log('exited due to rowcount');
                            process.exit(0);
                        }
                    });
                }
            });
        }
        let processedRows = 0;
        for (let i = 2; i < worksheet.rowCount+1; i++) { 
            if(processedRows === worksheet.rowCount-1){ 
                connection.end();
                console.log('exiting from the outer loop')
                process.exit(0);
            } else {
                processRow(i)
                processedRows++;
            }
        }
    })
    .catch(error => {
        console.log('Error:', error);
        process.exit(1); // Exit with an error code
    });

