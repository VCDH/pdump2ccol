/*
	pdump2ccol - converteer pdump naar ccol format
    Copyright (C) 2020-2021 Gemeente Den Haag, Netherlands
    Developed by Jasper Vries
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

document.getElementById('preview').onclick = function() {
    //get textarea contents
    var contents = document.getElementById('input').value;
    contents = process_contents(contents);
    //write new contents to textarea
    document.getElementById('input').value = contents;
 }

 document.getElementById('download').onclick = function() {
    //get textarea contents
    var contents = document.getElementById('input').value;
    contents = process_contents(contents);
    //offer file download
    download_file(contents);
 }

 document.getElementById('file').addEventListener('change', open_file, false);

 function process_contents(contents) {
    var matches = contents.matchAll(/(TO) (\d+) (\d+): (-?)(\d+)(\/te)/gi);
    matches = Array.from(matches);
    if (matches.length == 0) {
        return 'Geen ontruimingstijden gevonden in bestand';
    }
    var output = '   /* ontruimingstijden */';
    var prevfc = null;

    matches.forEach(function(match) {
        if (match[6] == '/te') {
            if ((prevfc != null) && (prevfc != match[2])) {
                output += "\r\n";
            } 
            output += "\r\n" + '   ' + match[1] + '_max[fc' + match[2] + '][fc' + match[3] + ']= ' + match[5] +';';
            prevfc = match[2];
        }
    });
    return output;
 }

 function download_file(content) {
    var a = document.createElement('a');
    var blob = new Blob([content], {type: 'text/plain'});
    var url = URL.createObjectURL(blob);
    a.setAttribute('href', url);
    a.setAttribute('download', 'TO_max.c');
    a.click();
    a.remove();
 }

function open_file(event) {
    var file = event.target.files[0];
    if (!file) {
        return;
    }
    var filereader = new FileReader();
    filereader.onload = function(event) {
        var contents = event.target.result;
        document.getElementById('input').value = contents;
    };
    filereader.readAsText(file);
}
