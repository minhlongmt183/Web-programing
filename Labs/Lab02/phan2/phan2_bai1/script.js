function tableCreate() {
    var body = document.getElementsByTagName('body')[0];
    var tbl = document.createElement('table');
    // tbl.style.width = '50%';
    tbl.setAttribute('border', '1');
    tbl.setAttribute('id', 'myTable');
    var tbdy = document.createElement('tbody');

    for (var i = 0; i < 2; i++) {
      var tr = document.createElement('tr');
        // tr.style.height = "20px";

      for (var j = 0; j < 2; j++) {
        var td = document.createElement('td');
        td.innerHTML = "INIT CELL"
        tr.appendChild(td)
      }
      tbdy.appendChild(tr);
    }
    tbl.appendChild(tbdy);
    body.appendChild(tbl)
}

function rowAdd(){    

    var table = document.getElementById("myTable");
    var row = table.insertRow(-1);

    for (i = 0; i < table.rows[0].cells.length; i++){
        var cell = row.insertCell(i);
        cell.innerHTML = "NEW CELL" + i;
    }
    

}

function columnAdd() {
    var table = document.getElementById("myTable");
    for (i = 0; i < table.rows.length; i++){
        var row = table.rows[i];
        var cell = row.insertCell(-1);
        cell.innerHTML = "NEW CELL";
    }
}

function colDelete(num){
    var table = document.getElementById("myTable");

    for (i = 0; i < table.rows.length; i++){
        row = table.rows[i];
        for(j = 0; j < num; j++)
            row.deleteCell(-1);

    }

}

function rowDelete(num){
    var table = document.getElementById("myTable");
    for (i = 0; i < num; i++){
        table.deleteRow(-1);
    }

}

function tableDelete(){
    var table = document.getElementById("myTable");
    while (table.rows.length > 0){
        table.deleteRow(0);
    }
}
