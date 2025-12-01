async function loadSellOut() {
    const apiURL = "http://lth.com.la/grownlthapi/v1/index.php";

    try {
        const response = await fetch(apiURL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorizationgrown": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjkyLCJ1c2VyTmFtZSI6ImFwaSIsImNvbXBhbnlJRCI6IidMVEgnIiwiZXhwIjoxNzk5OTUzMDE0fQ.fOU72uNHScOE_BG4JVz3_2GgRWArWPN8CriIpOuEFYk"
            },
            body: JSON.stringify({
                action: "getSellout"   // ถ้า API ไม่มี action ให้แจ้งผม
            })
        });

        const result = await response.json();
        const data = result.datas || [];

        let tableBody = "";

        data.forEach(item => {
            tableBody += `
                <tr>
                    <td>${item.InvoiceNo}</td>
                    <td>${item.SaleMan}</td>
                    <td>${item.CustomerName}</td>
                    <td>${item.Phone}</td>
                    <td>${item.InvoiceDate}</td>
                    <td>${item.BrandID}</td>
                    <td>${item.ModelID}</td>
                    <td>${item.ColorID}</td>
                    <td>${item.PhoneIMEI}</td>
                    <td>${item.QtyOrder}</td>
                    <td>${Number(item.NetPrice).toFixed(2)}</td>
                </tr>
            `;
        });

        document.getElementById("sellout-table").innerHTML = tableBody || `
            <tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>
        `;

    } catch (err) {
        console.error("API Error:", err);
        document.getElementById("sellout-table").innerHTML =
            `<tr><td colspan="11" style="text-align:center;color:red;">Error loading data</td></tr>`;
    }
}

// โหลดข้อมูลเมื่อเปิดหน้าเว็บ
window.onload = loadSellOut;
