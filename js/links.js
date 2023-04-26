
let aboutSection = document.getElementById("about");
let archiveDate = document.getElementById("gadates");
let archiveSection = document.querySelectorAll(".gadate");
let archiveFile = document.getElementById("archivefile");
let path = "archive/";

archiveSection.forEach(function(archiveDate){
    archiveDate.addEventListener("click", function() {
        archiveDate = this.getAttribute("id");
        console.log("Archive Data id = " + archiveDate);
        let url = `${path}GA${archiveDate}.html`;
        console.log("Archive File = " + url);
        aboutSection.classList.add("hidden");
        archiveFile.classList.remove("hidden");
        
        fetch(url).then(function(response) {
          return response.text();
        })
        .then(function(html) {
            console.log(html);
            archiveFile.innerHTML = html     
        });
    });
});
    
// $(document).ready(function(){
//     $("button").click(function(){
//       $("#div1").load("demo_test.txt");
//     });
//   });

    
