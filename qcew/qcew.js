(function (namespace){
    var Qcew = namespace.Qcew || {};
    
    var urlRoot = "http://localhost/qcew/"; //EX: http://www.yourserver.com/

    jsData = function (url) {
        var script = document.createElement("SCRIPT");
        var head = document.getElementsByTagName("head")[0];
        script.src = url;
        head.appendChild(script);
        head.removeChild(script);
    };
    processCsvJson = function (csvJson, callback) {
        var csvLines = csvJson.csvText.split("\r\n");
        var dataTable = [];
        for (var i = 0; i < csvLines.length; i += 1) {
            dataTable.push(csvLines[i].split(","))
        }
        callback(dataTable);
    };
    Qcew.getAreaData = function (year, qtr, area, callback) {
        jsData(urlRoot + "jsdata.php?year=" + year + "&qtr=" + qtr + "&area=" + area);
        (function returnData() {
            var csvJson;
            try {
                csvJson = Qcew[year][qtr]["area"][area];
                processCsvJson(csvJson, callback);
            } catch (er) {
                setTimeout(returnData,14);       
            }
        }());
    };
    Qcew.getIndustryData = function (year, qtr, industry, callback) {
        jsData(urlRoot + "jsdata.php?year=" + year + "&qtr=" + qtr + "&industry=" + industry);
        (function returnData() {
            var csvJson;
            try {
                csvJson = Qcew[year][qtr]["industry"][industry];
                processCsvJson(csvJson, callback);
            } catch (er) {
                setTimeout(returnData,14);       
            }
        }());
    };
    Qcew.getSizeData = function (year, size, callback) {
        jsData(urlRoot + "jsdata.php?year=" + year + "&size=" + size);
        (function returnData() {
            var csvJson;
            try {
                csvJson = Qcew[year]["1"]["size"][size];
                processCsvJson(csvJson, callback);
            } catch (er) {
                setTimeout(returnData,14);       
            }
        }());
    };
    namespace.Qcew = Qcew;
}(window));