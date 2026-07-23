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

    // 3. ประมวลผลสถิติ
    $totalEquipments = count($equipments);
    $statusSummary = ['ปกติ' => 0, 'ออฟไลน์' => 0, 'สัญญาณขาดหาย' => 0];
    $typeSummary = [];
    $issueCount = 0;

    foreach ($equipments as $item) {
        $status = $item['Connection_Status'] ?? 'ไม่ระบุ';
        if (isset($statusSummary[$status])) {
            $statusSummary[$status]++;
        } else {
            $statusSummary[$status] = 1;
        }

        $typeName = $item['TypeName'] ?? 'อื่นๆ';
        $typeSummary[$typeName] = ($typeSummary[$typeName] ?? 0) + 1;

        if (!empty($item['Issues_Found']) && $item['Issues_Found'] !== '-') {
            $issueCount++;
        }
    }

} catch (Exception $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Asset Management | Modern Dark Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #0f172a;        
            --card-bg: #1e293b;        
            --card-border: #334155;    
            --primary-blue: #3b82f6;   
            --text-main: #f8fafc;      
            --text-muted: #94a3b8;     
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ---------------------------------------------------- */
        /* 🌟 3D PRELOADER STYLES */
        /* ---------------------------------------------------- */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #0b0f19;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }

        #canvas3d-container {
            width: 280px;
            height: 280px;
            position: relative;
        }

        .preloader-text {
            margin-top: 15px;
            font-size: 1.1rem;
            font-weight: 500;
            color: #60a5fa;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: pulse-text 1.5s infinite alternate;
        }

        @keyframes pulse-text {
            0% { opacity: 0.4; }
            100% { opacity: 1; }
        }

        /* Modern Dark Card */
        .card-custom {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            transition: border-color 0.2s ease, transform 0.2s ease;
        }

        .card-custom:hover {
            border-color: #475569;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .clock-badge {
            font-family: 'Inter', sans-serif;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 30px;
            padding: 6px 18px;
            font-weight: 600;
            color: var(--primary-blue);
        }

        .table {
            color: var(--text-main) !important;
            margin-bottom: 0 !important;
        }

        .table thead th {
            background-color: #0f172a !important;
            color: var(--text-muted) !important;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px !important;
            border-bottom: 2px solid var(--card-border) !important;
        }

        .table tbody td {
            padding: 14px 16px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--card-border);
        }

        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }

        code {
            font-family: 'Inter', monospace;
            background-color: #0f172a;
            color: #60a5fa;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.825rem;
            border: 1px solid var(--card-border);
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-normal { background-color: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
        .status-offline { background-color: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .status-warning { background-color: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }

        .btn-filter {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-muted);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-filter:hover, .btn-filter.active {
            background-color: var(--primary-blue);
            color: #ffffff;
            border-color: var(--primary-blue);
        }
    </style>
</head>
<body>

<!-- 🌐 3D LOADING OVERLAY SCREEN -->
<div id="preloader">
    <div id="canvas3d-container"></div>
    <div class="preloader-text">
        <i class="fa-solid fa-spinner fa-spin me-2"></i>กำลังโหลดระบบครุภัณฑ์ 3D...
    </div>
</div>

<div class="container-fluid py-4 px-4">
    
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h3 class="fw-bold mb-1 text-white">
                <i class="fa-solid fa-server text-primary me-2"></i>ระบบทะเบียนครุภัณฑ์ไอที
            </h3>
            <p class="text-muted mb-0">ระบบบริหารจัดการ สรุปสถานะ และติดตามอุปกรณ์ไอทีในองค์กร</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="clock-badge d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fa-regular fa-clock text-primary"></i>
                <span id="liveClock">00:00:00 AM</span>
            </div>
        </div>
    </div>

    <!-- Stat Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 d-flex align-items-center">
                <div class="icon-box bg-primary bg-opacity-20 text-primary me-3">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <div>
                    <span class="text-muted d-block small">ครุภัณฑ์ทั้งหมด</span>
                    <h4 class="fw-bold mb-0 text-white"><?= $totalEquipments; ?> <small class="fs-6 text-muted fw-normal">รายการ</small></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 d-flex align-items-center">
                <div class="icon-box bg-success bg-opacity-20 text-success me-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <span class="text-muted d-block small">สถานะปกติ</span>
                    <h4 class="fw-bold mb-0 text-success"><?= $statusSummary['ปกติ'] ?? 0; ?> <small class="fs-6 text-muted fw-normal">เครื่อง</small></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 d-flex align-items-center">
                <div class="icon-box bg-danger bg-opacity-20 text-danger me-3">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <span class="text-muted d-block small">ออฟไลน์ / ขัดข้อง</span>
                    <h4 class="fw-bold mb-0 text-danger"><?= ($statusSummary['ออฟไลน์'] ?? 0) + ($statusSummary['สัญญาณขาดหาย'] ?? 0); ?> <small class="fs-6 text-muted fw-normal">เครื่อง</small></h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card-custom p-3 d-flex align-items-center">
                <div class="icon-box bg-warning bg-opacity-20 text-warning me-3">
                    <i class="fa-solid fa-wrench"></i>
                </div>
                <div>
                    <span class="text-muted d-block small">พบปัญหา/รอซ่อม</span>
                    <h4 class="fw-bold mb-0 text-warning"><?= $issueCount; ?> <small class="fs-6 text-muted fw-normal">รายการ</small></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h5 class="fw-bold mb-0 text-white"><i class="fa-solid fa-list-check me-2 text-primary"></i>รายการครุภัณฑ์</h5>
            <div class="d-flex gap-1 flex-wrap">
                <button class="btn btn-filter active" onclick="filterStatus('')">ทั้งหมด</button>
                <button class="btn btn-filter" onclick="filterStatus('ปกติ')">🟢 ปกติ</button>
                <button class="btn btn-filter" onclick="filterStatus('ออฟไลน์')">🔴 ออฟไลน์</button>
                <button class="btn btn-filter" onclick="filterStatus('สัญญาณขาดหาย')">🟡 สัญญาณขาดหาย</button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="equipmentTable" class="table align-middle w-100">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>ประเภทอุปกรณ์</th>
                        <th>Serial Number</th>
                        <th>ผู้ใช้งาน</th>
                        <th>หน่วยงาน</th>
                        <th>สถานที่ติดตั้ง</th>
                        <th>IP Address</th>
                        <th>สถานะ</th>
                        <th class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipments)): ?>
                        <?php foreach ($equipments as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= htmlspecialchars($row['EquipmentID']); ?></td>
                                <td>
                                    <span class="fw-semibold text-primary"><?= htmlspecialchars($row['TypeName'] ?? '-'); ?></span>
                                    <small class="text-muted d-block"><?= htmlspecialchars($row['CategoryName'] ?? '-'); ?></small>
                                </td>
                                <td><code><?= htmlspecialchars($row['SerialNumber'] ?? '-'); ?></code></td>
                                <td><?= htmlspecialchars($row['UserName'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['DepartmentName'] ?? '-'); ?></td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($row['Building'] ?? '-'); ?> 
                                        <?= $row['Floor'] ? 'ชั้น ' . htmlspecialchars($row['Floor']) : ''; ?>
                                    </small>
                                </td>
                                <td><code><?= htmlspecialchars($row['IP_Address'] ?? '-'); ?></code></td>
                                <td>
                                    <?php
                                        $statusClass = 'status-normal';
                                        $icon = 'fa-circle-check';
                                        if ($row['Connection_Status'] == 'ออฟไลน์') {
                                            $statusClass = 'status-offline';
                                            $icon = 'fa-circle-xmark';
                                        } elseif ($row['Connection_Status'] == 'สัญญาณขาดหาย') {
                                            $statusClass = 'status-warning';
                                            $icon = 'fa-triangle-exclamation';
                                        }
                                    ?>
                                    <span class="badge-status <?= $statusClass; ?>">
                                        <i class="fa-solid <?= $icon; ?>"></i>
                                        <?= htmlspecialchars($row['Connection_Status'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary text-white rounded-pill px-3 fw-medium" 
                                            onclick='openDetailModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                        <i class="fa-solid fa-magnifying-glass me-1 text-primary"></i>รายละเอียด
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold mb-3 text-white"><i class="fa-solid fa-chart-pie text-primary me-2"></i>สัดส่วนสถานะการเชื่อมต่อ</h6>
                <div style="height: 250px; position: relative;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold mb-3 text-white"><i class="fa-solid fa-chart-bar text-primary me-2"></i>จำนวนอุปกรณ์จำแนกตามประเภท</h6>
                <div style="height: 250px; position: relative;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal รายละเอียดครุภัณฑ์ -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card-custom border-secondary text-white">
            <div class="modal-header border-bottom border-secondary px-4">
                <h5 class="modal-title fw-bold text-white" id="modalTitle">
                    <i class="fa-solid fa-laptop me-2 text-primary"></i>รายละเอียดอุปกรณ์
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3" id="modalBodyContent">
                    <!-- เติมข้อมูลด้วย JS -->
                </div>
            </div>
            <div class="modal-footer border-top border-secondary px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- 🌟 THREE.JS LIBRARY (สำหรับสร้างโมเดล 3D) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<script>
    let table;

    // ----------------------------------------------------
    // 🎲 1. THREE.JS 3D MODEL PRELOADER ENGINE
    // ----------------------------------------------------
    function init3DPreloader() {
        const container = document.getElementById('canvas3d-container');
        const scene = new THREE.Scene();

        const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 1000);
        camera.position.z = 5;

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(280, 280);
        container.appendChild(renderer.domElement);

        // สร้างโมเดล 3D Tech Core (Wireframe Box + Inner Glowing Sphere)
        const outerGroup = new THREE.Group();

        // 3D Outer Cube Wireframe
        const cubeGeo = new THREE.BoxGeometry(1.8, 1.8, 1.8);
        const cubeMat = new THREE.MeshBasicMaterial({ color: 0x3b82f6, wireframe: true });
        const outerCube = new THREE.Mesh(cubeGeo, cubeMat);
        outerGroup.add(outerCube);

        // 3D Inner Icosahedron Core
        const coreGeo = new THREE.IcosahedronGeometry(0.8, 1);
        const coreMat = new THREE.MeshBasicMaterial({ color: 0x60a5fa, wireframe: true });
        const innerCore = new THREE.Mesh(coreGeo, coreMat);
        outerGroup.add(innerCore);

        scene.add(outerGroup);

        // Animation Loop
        function animate() {
            requestAnimationFrame(animate);
            outerCube.rotation.x += 0.01;
            outerCube.rotation.y += 0.015;
            innerCore.rotation.x -= 0.02;
            innerCore.rotation.y -= 0.01;
            renderer.render(scene, camera);
        }
        animate();
    }

    // เริ่มสร้างโมเดล 3D ทันที
    init3DPreloader();

    // ซ่อน Preloader เมื่อโหลดหน้าเว็บเสร็จ
    window.addEventListener('load', function() {
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            preloader.style.opacity = '0';
            preloader.style.visibility = 'hidden';
        }, 1200); // หน่วงเวลา 1.2 วินาทีเพื่อให้เห็น Animation 3D สวยๆ
    });

    // ----------------------------------------------------
    // 📊 2. DASHBOARD SCRIPTS
    // ----------------------------------------------------
    $(document).ready(function() {
        // Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: true });
            $('#liveClock').text(timeStr);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // DataTables
        table = $('#equipmentTable').DataTable({
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "🔍 ค้นหาข้อมูล...",
                "lengthMenu": "แสดง _MENU_ รายการ",
                "zeroRecords": "ไม่พบข้อมูล",
                "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                "paginate": { "next": "ถัดไป", "previous": "ก่อนหน้า" }
            },
            "pageLength": 8,
            "order": [[ 0, "asc" ]]
        });

        // Filter Pills
        $('.btn-filter').click(function() {
            $('.btn-filter').removeClass('active');
            $(this).addClass('active');
        });

        // Charts
        renderCharts();
    });

    function filterStatus(statusKeyword) {
        table.column(7).search(statusKeyword).draw();
    }

    function openDetailModal(data) {
        $('#modalTitle').html(`<i class="fa-solid fa-microchip text-primary me-2"></i> ${data.TypeName || 'อุปกรณ์'} (SN: ${data.SerialNumber || 'N/A'})`);
        
        let html = `
            <div class="col-md-6">
                <div class="p-3 bg-dark bg-opacity-50 rounded-3 border border-secondary">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-gear me-2"></i>ข้อมูลผู้ใช้และสถานที่</h6>
                    <p class="mb-2"><strong>ผู้รับผิดชอบ:</strong> ${data.UserName || '-'}</p>
                    <p class="mb-2"><strong>หน่วยงาน:</strong> ${data.DepartmentName || '-'}</p>
                    <p class="mb-2"><strong>สถานที่:</strong> ${data.Building || '-'} ${data.Floor ? 'ชั้น ' + data.Floor : ''} (${data.Room || '-'})</p>
                    <p class="mb-2"><strong>ปีที่ได้รับ:</strong> ${data.ReceiveYear || '-'}</p>
                    <p class="mb-0"><strong>แหล่งงบประมาณ:</strong> ${data.BudgetName || '-'}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-dark bg-opacity-50 rounded-3 border border-secondary">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-sliders me-2"></i>สเปกและระบบเครือข่าย</h6>
                    <p class="mb-2"><strong>CPU:</strong> ${data.CPU_Model || '-'}</p>
                    <p class="mb-2"><strong>RAM:</strong> ${data.RAM_GB ? data.RAM_GB + ' GB' : '-'}</p>
                    <p class="mb-2"><strong>Storage:</strong> ${data.Storage_Capacity || '-'}</p>
                    <p class="mb-2"><strong>OS/Firmware:</strong> ${data.OS_Firmware || '-'}</p>
                    <p class="mb-2"><strong>IP Address:</strong> <code>${data.IP_Address || '-'}</code></p>
                    <p class="mb-0"><strong>MAC Address:</strong> <code>${data.MAC_Address || '-'}</code></p>
                </div>
            </div>
            <div class="col-12">
                <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3">
                    <h6 class="fw-bold text-warning mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>ปัญหาที่พบ / ข้อเสนอแนะ</h6>
                    <p class="text-danger mb-1"><strong>ปัญหา:</strong> ${data.Issues_Found || '-'}</p>
                    <p class="text-muted mb-0"><strong>หมายเหตุ:</strong> ${data.Remarks || '-'}</p>
                </div>
            </div>
        `;

        $('#modalBodyContent').html(html);
        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }

    function renderCharts() {
        const statusData = <?= json_encode($statusSummary); ?>;
        const typeData = <?= json_encode($typeSummary); ?>;

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#4ade80', '#f87171', '#fbbf24', '#94a3b8'],
                    borderWidth: 2,
                    borderColor: '#1e293b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { family: 'Prompt' } } }
                }
            }
        });

        new Chart(document.getElementById('typeChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    label: 'จำนวนเครื่อง',
                    data: Object.values(typeData),
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    hoverBackgroundColor: '#60a5fa'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8', stepSize: 1 } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { family: 'Prompt' } } 
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
</script>

</body>
</html>