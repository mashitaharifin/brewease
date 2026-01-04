<?php
// reports.php
require_once '../classes/DBConnection.php';

$db = new DBConnection();
$conn = $db->getConnection();


// Handle date filters
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Total Revenue & Orders
$sql = "
  SELECT 
    COUNT(DISTINCT o.id) AS total_orders,
    SUM(oi.quantity * oi.price) AS total_revenue
  FROM orders o
  JOIN order_items oi ON o.id = oi.order_id
  WHERE DATE(o.created_at) BETWEEN ? AND ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalOrders = $result['total_orders'] ?? 0;
$totalRevenue = $result['total_revenue'] ?? 0;

// Top 5 Products
$sql = "
  SELECT pl.name, SUM(oi.quantity) AS total_sold
  FROM order_items oi
  JOIN product_list pl ON oi.product_id = pl.id
  JOIN orders o ON oi.order_id = o.id
  WHERE DATE(o.created_at) BETWEEN ? AND ?
  GROUP BY oi.product_id
  ORDER BY total_sold DESC
  LIMIT 5
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$topProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Least Purchased Products
$sql = "
  SELECT pl.name, SUM(oi.quantity) AS total_sold
  FROM order_items oi
  JOIN product_list pl ON oi.product_id = pl.id
  JOIN orders o ON oi.order_id = o.id
  WHERE DATE(o.created_at) BETWEEN ? AND ?
  GROUP BY oi.product_id
  ORDER BY total_sold ASC
  LIMIT 5
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$leastProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Top Customer
$sql = "
  SELECT c.fullname, SUM(oi.quantity * oi.price) AS total_spent
  FROM orders o
  JOIN order_items oi ON o.id = oi.order_id
  JOIN customers c ON o.customer_id = c.id
  WHERE DATE(o.created_at) BETWEEN ? AND ?
  GROUP BY c.id
  ORDER BY total_spent DESC
  LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$topCustomer = $stmt->get_result()->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reports & Analytics - BrewEase Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jsPDF and autoTable CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">
<?php include 'navbar.php'; ?>

<div class="max-w-7xl mx-auto px-4 pt-5">
  <header class="mb-6">
    <h1 class="text-3xl font-bold text-white mb-6">Reports & Analytics</h1>
    </header>

    <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
        <label for="start_date" class="text-sm text-white font-bold">From:</label>
        <input
            type="date"
            id="start_date"
            name="start_date"
            value="<?= htmlspecialchars($startDate) ?>"
            class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold"
            required
        >

        <label for="end_date" class="text-sm text-white font-bold">To:</label>
        <input
            type="date"
            id="end_date"
            name="end_date"
            value="<?= htmlspecialchars($endDate) ?>"
            class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold"
            required
        >

        <button type="submit" class="bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">
            Filter
        </button>


        <!-- Export buttons -->
        <button
            type="button"
            id="exportPdf"
            class="bg-white text-[#AD85FD] hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300"
        >
            Export PDF
        </button>
        <button
            type="button"
            id="exportCsv"
            class="bg-white text-[#AD85FD] hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300"
        >
            Export CSV
        </button>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-800 text-lg font-bold">Total Revenue</p>
            <p class="text-xl font-bold text-green-600">RM <?= number_format($totalRevenue, 2) ?></p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-800 text-lg font-bold">Total Orders</p>
            <p class="text-xl font-bold text-blue-600"><?= $totalOrders ?></p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <p class="text-gray-800 text-lg font-bold">Top Customer</p>
            <?php if ($topCustomer): ?>
            <p class="text-xl font-bold text-purple-600"><?= htmlspecialchars($topCustomer['fullname']) ?></p>
            <p class="text-sm text-gray-600">RM <?= number_format($topCustomer['total_spent'], 2) ?></p>
            <?php else: ?>
            <p class="text-purple-600 font-semibold">No customers found for this date.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Products Lists -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-4 rounded-2xl shadow">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Top 5 Products</h2>
            <ul id="topProductsList" class="list-disc list-inside text-gray-700">
                <?php foreach ($topProducts as $p): ?>
                    <li><?= htmlspecialchars($p['name']) ?> - <?= $p['total_sold'] ?> sold</li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Least Purchased Products</h2>
            <ul id="leastProductsList" class="list-disc list-inside text-gray-700">
                <?php foreach ($leastProducts as $p): ?>
                    <li><?= htmlspecialchars($p['name']) ?> - <?= $p['total_sold'] ?> sold</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    </div>
    </body>
</html>

<!-- Export PDF & CSV Script -->
<script>
    // PDF Export
        document.getElementById('exportPdf').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const startDate = "<?= htmlspecialchars($startDate) ?>";
        const endDate = "<?= htmlspecialchars($endDate) ?>";

        // Title
        doc.setFontSize(14);
        doc.text(`Sales Report (${startDate} to ${endDate})`, 14, 15);

        // Summary section as simple text
        doc.setFontSize(12);
        let y = 25;
        const lineHeight = 7;

        const totalRevenue = "RM <?= number_format($totalRevenue, 2) ?>";
        const totalOrders = "<?= $totalOrders ?>";
        const topCustomerName = "<?= addslashes(htmlspecialchars($topCustomer['fullname'])) ?>";
        const topCustomerSpent = "RM <?= number_format($topCustomer['total_spent'], 2) ?>";

        doc.text(`Total Revenue: ${totalRevenue}`, 14, y);
        y += lineHeight;
        doc.text(`Total Orders: ${totalOrders}`, 14, y);
        y += lineHeight;
        doc.text(`Top Customer: ${topCustomerName} (${topCustomerSpent})`, 14, y);
        y += lineHeight * 2;

        // Helper to convert <ul> list to array of rows
        function getListItems(id) {
        const items = [];
        document.querySelectorAll(`#${id} li`).forEach(li => {
            items.push([li.innerText]);
        });
        return items;
        }

        // Top Products table
        const topProductsHeader = ['Top 5 Products (Name - Sold)'];
        const topProductsBody = getListItems('topProductsList');

        doc.autoTable({
        head: [topProductsHeader],
        body: topProductsBody,
        startY: y,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [99, 102, 241] }, // Indigo 600
        margin: { left: 14, right: 14 }
        });

        y = doc.lastAutoTable.finalY + 10;

        // Least Products table
        const leastProductsHeader = ['Least Purchased Products (Name - Sold)'];
        const leastProductsBody = getListItems('leastProductsList');

        doc.autoTable({
        head: [leastProductsHeader],
        body: leastProductsBody,
        startY: y,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [99, 102, 241] },
        margin: { left: 14, right: 14 }
        });

        doc.save(`sales_report_${startDate}_to_${endDate}.pdf`);
    });

    // CSV Export
    document.getElementById('exportCsv').addEventListener('click', () => {
        const startDate = "<?= htmlspecialchars($startDate) ?>";
        const endDate = "<?= htmlspecialchars($endDate) ?>";

        // Helper to convert list items to CSV rows
        function listToCsvRows(id) {
        const rows = [];
        document.querySelectorAll(`#${id} li`).forEach(li => {
            // Escape commas by wrapping in quotes if needed
            let text = li.innerText;
            if (text.includes(',')) {
            ext = `"${text}"`;
            }
            rows.push(text);
        });
        return rows;
        }

        // Build CSV content
        let csvContent = `Sales Report (${startDate} to ${endDate})\n\n`;
        csvContent += `Total Revenue,RM <?= number_format($totalRevenue, 2) ?>\n`;
        csvContent += `Total Orders,<?= $totalOrders ?>\n`;
        csvContent += `Top Customer,<?= addslashes($topCustomer['fullname']) ?>\n`;
        csvContent += `Top Customer Spent,RM <?= number_format($topCustomer['total_spent'], 2) ?>\n\n`;

        csvContent += "Top 5 Products\n";
        csvContent += listToCsvRows('topProductsList').join("\n") + "\n\n";

        csvContent += "Least Purchased Products\n";
        csvContent += listToCsvRows('leastProductsList').join("\n") + "\n";


        // Download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `sales_report_${startDate}_to_${endDate}.csv`);
        document.body.appendChild(link);
        link.click();
    document.body.removeChild(link);
    })
    </script>