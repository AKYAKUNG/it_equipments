<?php
// 1. เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db.php'; 

try {
    // 2. คำสั่ง SQL JOIN ดึงข้อมูลทั้งหมด
    $sql = "SELECT 
                e.*,
                dt.CategoryName, dt.TypeName,
                l.Building, l.Floor, l.Room,
                u.FullName AS UserName,
                d.DepartmentName,
                b.BudgetName
            FROM IT_Equipments e
            LEFT JOIN Device_Types dt ON e.TypeID = dt.TypeID
            LEFT JOIN Locations l ON e.LocationID = l.LocationID
            LEFT JOIN Users u ON e.UserID = u.UserID
            LEFT JOIN Departments d ON u.DepartmentID = d.DepartmentID
            LEFT JOIN Budgets b ON e.BudgetID = b.BudgetID
            ORDER BY e.EquipmentID ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. ประมวลผลข้อมูลสำหรับสร้างกราฟ
    $statusSummary = ['ปกติ' => 0, 'ออฟไลน์' => 0, 'สัญญาณขาดหาย' => 0];
    $typeSummary = [];

    foreach ($equipments as $item) {
        // นับจำนวนตามสถานะการเชื่อมต่อ
        $status = $item['Connection_Status'] ?? 'ไม่ระบุ';
        if (array_key_exists($status, $statusSummary)) {
            $statusSummary[$status]++;
        } else {
            $statusSummary[$status] = 1;
        }

        // นับจำนวนตามประเภทอุปกรณ์
        $typeName = $item['TypeName'] ?? 'อื่นๆ';
        if (!isset($typeSummary[$typeName])) {
            $typeSummary[$typeName] = 0;
        }
        $typeSummary[$typeName]++;
    }

} catch (Exception $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบทะเบียนครุภัณฑ์ไอที & Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Google Font (Prompt) -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f4f6f9;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #edf2f7;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            font-weight: 600;
        }
        .table th {
            background-color: #1f4e78 !important;
            color: #ffffff !important;
            vertical-align: middle;
            font-weight: 500;
            white-space: nowrap;
        }
        .table td {
            vertical-align: middle;
            font-size: 0.88rem;
        }
        /* Custom Status Badge */
        .badge-status-normal { background-color: #d1e7dd; color: #0f5132; }
        .badge-status-offline { background-color: #f8d7da; color: #842029; }
        .badge-status-warning { background-color: #fff3cd; color: #664d03; }
        .chart-container { position: relative; height: 280px; width: 100%; }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-md-12">
            <h3 class="fw-bold text-dark">📦 ระบบทะเบียนครุภัณฑ์ไอที & แดชบอร์ด</h3>
            <p class="text-muted">สรุปภาพรวมและรายการอุปกรณ์ทั้งหมดในระบบ</p>
        </div>
    </div>

    <!-- ตารางข้อมูลหลัก -->
    <div class="card p-3">
        <div class="card-header bg-transparent ps-0 mb-2">
            📊 รายการครุภัณฑ์ทั้งหมด
        </div>
        <div class="table-responsive">
            <table id="equipmentTable" class="table table-hover table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>ประเภทอุปกรณ์</th>
                        <th>Serial Number</th>
                        <th>ผู้ใช้งาน</th>
                        <th>หน่วยงาน</th>
                        <th>สถานที่ติดตั้ง</th>
                        <th>สเปก (CPU/RAM/Storage)</th>
                        <th>IP Address</th>
                        <th>สถานะเชื่อมต่อ</th>
                        <th>สภาพการใช้งาน</th>
                        <th>ปัญหาที่พบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipments)): ?>
                        <?php foreach ($equipments as $row): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= htmlspecialchars($row['EquipmentID']); ?></td>
                                <td>
                                    <span class="fw-bold text-primary"><?= htmlspecialchars($row['TypeName'] ?? '-'); ?></span><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['CategoryName'] ?? '-'); ?></small>
                                </td>
                                <td><code><?= htmlspecialchars($row['SerialNumber'] ?? '-'); ?></code></td>
                                <td><?= htmlspecialchars($row['UserName'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['DepartmentName'] ?? '-'); ?></td>
                                <td>
                                    <?= htmlspecialchars($row['Building'] ?? '-'); ?> 
                                    <?= $row['Floor'] ? 'ชั้น ' . htmlspecialchars($row['Floor']) : ''; ?>
                                    <small class="text-muted d-block"><?= htmlspecialchars($row['Room'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <small>
                                        <strong>CPU:</strong> <?= htmlspecialchars($row['CPU_Model'] ?? '-'); ?><br>
                                        <strong>RAM:</strong> <?= $row['RAM_GB'] ? htmlspecialchars($row['RAM_GB']) . ' GB' : '-'; ?> | 
                                        <strong>Storage:</strong> <?= htmlspecialchars($row['Storage_Capacity'] ?? '-'); ?>
                                    </small>
                                </td>
                                <td><code><?= htmlspecialchars($row['IP_Address'] ?? '-'); ?></code></td>
                                <td>
                                    <?php
                                        $statusClass = 'badge-status-normal';
                                        if ($row['Connection_Status'] == 'ออฟไลน์') {
                                            $statusClass = 'badge-status-offline';
                                        } elseif ($row['Connection_Status'] == 'สัญญาณขาดหาย') {
                                            $statusClass = 'badge-status-warning';
                                        }
                                    ?>
                                    <span class="badge <?= $statusClass; ?> px-2 py-1">
                                        <?= htmlspecialchars($row['Connection_Status'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['Condition_Status'] ?? '-'); ?></td>
                                <td class="text-danger">
                                    <small><?= htmlspecialchars($row['Issues_Found'] ?? '-'); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ส่วนแสดงกราฟสรุปข้อมูล ( Charts Section ) ต่อท้ายตาราง -->
    <div class="row">
        <!-- กราฟที่ 1: สถานะการเชื่อมต่อ -->
        <div class="col-lg-5">
            <div class="card p-3">
                <div class="card-header">
                    🟢 สรุปสถานะการเชื่อมต่อ (Connection Status)
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- กราฟที่ 2: จำนวนอุปกรณ์จำแนกตามประเภท -->
        <div class="col-lg-7">
            <div class="card p-3">
                <div class="card-header">
                    💻 จำนวนอุปกรณ์จำแนกตามประเภท (Equipment by Type)
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // 1. เปิดใช้งาน DataTables
        $('#equipmentTable').DataTable({
            "language": {
                "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
                "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                "infoEmpty": "ไม่มีข้อมูล",
                "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                "search": "🔍 ค้นหา:",
                "paginate": {
                    "first": "หน้าแรก", "last": "หน้าสุดท้าย", "next": "ถัดไป", "previous": "ก่อนหน้า"
                }
            },
            "pageLength": 10,
            "order": [[ 0, "asc" ]]
        });

        // 2. ข้อมูลจาก PHP สำหรับสร้างกราฟ
        const statusData = <?= json_encode($statusSummary); ?>;
        const typeData = <?= json_encode($typeSummary); ?>;

        // 3. กราฟสรุปสถานะการเชื่อมต่อ (Doughnut Chart)
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#198754', '#dc3545', '#ffc107', '#6c757d'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 4. กราฟสรุปประเภทอุปกรณ์ (Bar Chart)
        const ctxType = document.getElementById('typeChart').getContext('2d');
        new Chart(ctxType, {
            type: 'bar',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    label: 'จำนวน (เครื่อง/ชิ้น)',
                    data: Object.values(typeData),
                    backgroundColor: '#1f4e78',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>

</body>
</html>