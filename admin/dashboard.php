<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('index.php');
}

// Handle verification actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc_id = $_POST['doc_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    
    if ($doc_id && in_array($action, ['verify', 'reject'])) {
        $status = $action === 'verify' ? 'verified' : 'rejected';
        $stmt = $pdo->prepare("UPDATE documents SET status = ?, remarks = ?, verified_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $remarks, $doc_id]);
        
        redirect('dashboard.php?success=Document ' . $status . ' successfully');
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$type_filter = $_GET['type'] ?? 'all';

// Build query
$query = "SELECT d.*, u.full_name, u.mobile FROM documents d JOIN users u ON d.user_id = u.id WHERE 1=1";
$params = [];

if ($filter !== 'all') {
    $query .= " AND d.status = ?";
    $params[] = $filter;
}

if ($type_filter !== 'all') {
    $query .= " AND d.document_type = ?";
    $params[] = $type_filter;
}

$query .= " ORDER BY d.submitted_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$documents = $stmt->fetchAll();

// Get stats
$stmt = $pdo->query("SELECT COUNT(*) FROM documents WHERE status = 'pending'");
$pendingCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM documents WHERE status = 'verified'");
$verifiedCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Nagarik App</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            font-size: 36px;
            color: #1e3c72;
            margin-bottom: 10px;
        }
        .stat-card p {
            color: #666;
        }
        .filter-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-bar select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .doc-images {
            display: flex;
            gap: 10px;
        }
        .doc-images img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="header-logo">
                    <span>नागरिक</span>
                </div>
                <div class="header-title">
                    <h1>Nagarik App</h1>
                    <p>Admin Portal</p>
                </div>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span><?php echo sanitize($_SESSION['admin_username']); ?></span>
                </div>
                <div class="header-actions">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <h2 class="section-title">ADMIN DASHBOARD</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 20px;">
                    <?php echo sanitize($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $pendingCount; ?></h3>
                    <p>Pending Verifications</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $verifiedCount; ?></h3>
                    <p>Verified Documents</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filter-bar">
                <span><strong>Filters:</strong></span>
                <select onchange="applyFilter('filter', this.value)">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="verified" <?php echo $filter === 'verified' ? 'selected' : ''; ?>>Verified</option>
                    <option value="rejected" <?php echo $filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <select onchange="applyFilter('type', this.value)">
                    <option value="all" <?php echo $type_filter === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="nid" <?php echo $type_filter === 'nid' ? 'selected' : ''; ?>>National ID</option>
                    <option value="citizenship" <?php echo $type_filter === 'citizenship' ? 'selected' : ''; ?>>Citizenship</option>
                </select>
            </div>
            
            <!-- Documents Table -->
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Mobile</th>
                        <th>Type</th>
                        <th>Doc Number</th>
                        <th>Images</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px;">No documents found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?php echo $doc['id']; ?></td>
                                <td><?php echo sanitize($doc['full_name']); ?></td>
                                <td><?php echo sanitize($doc['mobile']); ?></td>
                                <td><?php echo $doc['document_type'] === 'nid' ? 'National ID' : 'Citizenship'; ?></td>
                                <td><?php echo sanitize($doc['document_number']); ?></td>
                                <td>
                                    <div class="doc-images">
                                        <img src="../<?php echo sanitize($doc['front_image']); ?>" alt="Front" onclick="viewImage('../<?php echo sanitize($doc['front_image']); ?>')">
                                        <?php if ($doc['back_image']): ?>
                                            <img src="../<?php echo sanitize($doc['back_image']); ?>" alt="Back" onclick="viewImage('../<?php echo sanitize($doc['back_image']); ?>')">
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $doc['status']; ?>">
                                        <?php echo ucfirst($doc['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($doc['submitted_at'])); ?></td>
                                <td>
                                    <?php if ($doc['status'] === 'pending'): ?>
                                        <div class="action-buttons">
                                            <button class="btn-view" onclick="viewDocument(<?php echo $doc['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-verify" onclick="verifyDocument(<?php echo $doc['id']; ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn-reject" onclick="rejectDocument(<?php echo $doc['id']; ?>)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <button class="btn-view" onclick="viewDocument(<?php echo $doc['id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    
    <!-- View Document Modal -->
    <div class="modal" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Document Details</h3>
                <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>
    
    <!-- Image View Modal -->
    <div class="modal" id="imageModal" onclick="closeModal('imageModal')">
        <div class="modal-content" style="background: transparent; box-shadow: none; text-align: center;" onclick="event.stopPropagation()">
            <img id="modalImage" src="" style="max-width: 100%; max-height: 80vh; border-radius: 10px;">
        </div>
    </div>
    
    <!-- Action Form -->
    <form id="actionForm" method="POST" style="display: none;">
        <input type="hidden" name="doc_id" id="actionDocId">
        <input type="hidden" name="action" id="actionType">
        <input type="hidden" name="remarks" id="actionRemarks">
    </form>
    
    <script>
        function applyFilter(type, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(type, value);
            window.location.href = url.toString();
        }
        
        function viewImage(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.add('active');
        }
        
        function viewDocument(docId) {
            // For simplicity, redirect to a view page or show modal with full details
            alert('Document ID: ' + docId + '\n\nClick on images to view them in full size.');
        }
        
        function verifyDocument(docId) {
            if (confirm('Are you sure you want to verify this document?')) {
                document.getElementById('actionDocId').value = docId;
                document.getElementById('actionType').value = 'verify';
                document.getElementById('actionRemarks').value = '';
                document.getElementById('actionForm').submit();
            }
        }
        
        function rejectDocument(docId) {
            const reason = prompt('Please enter the reason for rejection:');
            if (reason !== null) {
                document.getElementById('actionDocId').value = docId;
                document.getElementById('actionType').value = 'reject';
                document.getElementById('actionRemarks').value = reason;
                document.getElementById('actionForm').submit();
            }
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
    </script>
</body>
</html>
