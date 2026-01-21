<?php
require_once '../includes/db.php';

// Session already handled by header.php or robust check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin yetki kontrolü
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Mesaj silme işlemi
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['msg_success'] = "Mesaj başarıyla silindi.";
    header("Location: messages.php");
    exit;
}

// Durum güncelleme (Okundu/Okunmadı)
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $stmt = $pdo->prepare("UPDATE messages SET status = 1 - status WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: messages.php");
    exit;
}

// Mesajları çek
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

include 'header.php';
?>

<div class="content-body" style="padding: 0; background: #f8fafc; min-height: 100vh;">
    <!-- Page Title & Stats -->
    <div class="dashboard-header" style="align-items: flex-end;">
        <div>
            <h1>Gelen Mesajlar</h1>
            <p>İletişim formu üzerinden gelen tüm talepleri buradan yönetebilirsiniz.</p>
        </div>
        <div style="display: flex; gap: 15px; margin-top: 15px;">
            <div style="background: #fff; padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid var(--admin-accent);">
                <span style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;">Toplam</span>
                <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 5px;"><?php echo count($messages); ?></div>
            </div>
            <?php 
            $newCount = count(array_filter($messages, function($m) { return $m['status'] == 0; }));
            if ($newCount > 0): 
            ?>
            <div style="background: #fff; padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid #ef4444;">
                <span style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;">Yeni</span>
                <div style="font-size: 20px; font-weight: 800; color: #ef4444; margin-top: 5px;"><?php echo $newCount; ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['msg_success'])): ?>
        <div style="background: #dcfce7; color: #166534; padding: 18px 25px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 15px; animation: slideIn 0.4s ease-out;">
            <i class="fas fa-check-circle" style="font-size: 20px;"></i>
            <p style="margin: 0; font-weight: 600;"><?php echo $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?></p>
        </div>
    <?php endif; ?>

    <div class="admin-card" style="background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 10px 40px rgba(0,0,0,0.03); overflow: hidden;">
        <?php if (empty($messages)): ?>
            <div style="text-align: center; padding: 100px 40px;">
                <div style="width: 100px; height: 100px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                    <i class="fas fa-inbox" style="font-size: 40px; color: #cbd5e1;"></i>
                </div>
                <h3 style="color: #0f172a; font-weight: 700; margin-bottom: 10px;">Henüz Mesaj Yok</h3>
                <p style="color: #64748b; margin: 0;">Web sitenizden iletilen bir mesaj şu an bulunmuyor.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive-container">
                <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
                    <thead>
                        <tr style="background: #f8fafc; text-align: left;">
                            <th style="padding: 20px 25px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Durum</th>
                            <th style="padding: 20px 25px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Gönderen</th>
                            <th style="padding: 20px 25px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Hizmet / Konu</th>
                            <th style="padding: 20px 25px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Mesaj Önizleme</th>
                            <th style="padding: 20px 25px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right;">Eylem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="message-row" onclick="viewMessage(<?php echo htmlspecialchars(json_encode($msg)); ?>, event)" style="border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: all 0.2s;">
                                <td style="padding: 25px;">
                                    <?php if ($msg['status'] == 0): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; background: #fee2e2; color: #ef4444; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                                            <span style="width: 6px; height: 6px; background: #ef4444; border-radius: 50%; display: inline-block;"></span> Yeni
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; color: #64748b; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                            Okundu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 25px;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--admin-accent); text-transform: uppercase;">
                                            <?php echo mb_substr($msg['name'] ?? '', 0, 1); ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px;"><?php echo htmlspecialchars($msg['name']); ?></div>
                                            <div style="font-size: 13px; color: #64748b;"><?php echo htmlspecialchars($msg['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 25px;">
                                    <span style="display: inline-block; background: rgba(var(--admin-accent-rgb), 0.08); color: var(--admin-accent); padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                        <?php echo htmlspecialchars($msg['service'] ?: 'Genel Tercüme'); ?>
                                    </span>
                                    <div style="margin-top: 5px; font-size: 11px; color: #94a3b8; font-weight: 600;">
                                        <i class="far fa-clock" style="margin-right: 4px;"></i> <?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?>
                                    </div>
                                </td>
                                <td style="padding: 25px;">
                                    <div style="max-width: 300px; color: #475569; font-size: 14px; line-height: 1.5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                </td>
                                <td style="padding: 25px; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="action-btn-view" style="width: 36px; height: 36px; border-radius: 8px; border: none; background: #f1f5f9; color: #0f172a; cursor: pointer; transition: all 0.2s;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?delete=<?php echo $msg['id']; ?>" onclick="event.stopPropagation(); return confirm('Bu mesajı silmek istediğinize emin misiniz?')" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; border: none; background: #fee2e2; color: #ef4444; cursor: pointer; transition: all 0.2s;">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Premium Modal -->
<div id="messageModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 650px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; overflow: hidden; animation: modalSlide 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);">
        
        <!-- Modal Header -->
        <div style="padding: 30px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div id="modalInitial" style="width: 45px; height: 45px; background: var(--admin-accent); color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800;"></div>
                <div>
                    <h3 id="modalSenderName" style="margin: 0; color: #0f172a; font-size: 18px; font-weight: 800;"></h3>
                    <p id="modalSenderEmail" style="margin: 2px 0 0 0; color: #64748b; font-size: 13px; font-weight: 500;"></p>
                </div>
            </div>
            <button onclick="closeModal()" style="width: 36px; height: 36px; background: #fff; border: 1px solid #e2e8f0; border-radius: 50%; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px;">
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Talep Türü</div>
                    <div id="modalService" style="background: #f1f5f9; padding: 8px 15px; border-radius: 8px; color: #0f172a; font-weight: 700; font-size: 14px; display: inline-block;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Gönderim Tarihi</div>
                    <div id="modalDate" style="color: #0f172a; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                        <i class="far fa-calendar-alt" style="color: var(--admin-accent);"></i> <span id="modalDateVal"></span>
                    </div>
                </div>
            </div>

            <div>
                <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">Mesaj İçeriği</div>
                <div id="modalMessage" style="background: #f8fafc; padding: 25px; border-radius: 16px; color: #334155; font-size: 15px; line-height: 1.8; border: 1px solid #f1f5f9; max-height: 300px; overflow-y: auto; white-space: pre-wrap;"></div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 25px 30px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 12px;">
            <a id="modalReplyBtn" href="#" style="background: var(--admin-accent); color: #fff; padding: 12px 25px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(var(--admin-accent-rgb), 0.3);">
                <i class="fas fa-reply"></i> Yanıtla
            </a>
            <button onclick="closeModal()" style="background: #fff; border: 1px solid #e2e8f0; color: #475569; padding: 12px 25px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer;">
                Kapat
            </button>
        </div>
    </div>
</div>

<style>
    .message-row:hover {
        background: #fdfdfd !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }
    .message-row:hover .action-btn-view {
        background: var(--admin-accent) !important;
        color: #fff !important;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes modalSlide {
        from { opacity: 0; transform: translateY(30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

<script>
function viewMessage(msg, event) {
    // Stop propagation is handled by the links themselves (delete/toggle)
    // No need to block button clicks here as they are part of the row experience
    
    const modal = document.getElementById('messageModal');
    
    // Fill Modal Data
    document.getElementById('modalInitial').textContent = msg.name.substring(0, 1).toUpperCase();
    document.getElementById('modalSenderName').textContent = msg.name;
    document.getElementById('modalSenderEmail').textContent = msg.email;
    document.getElementById('modalService').textContent = msg.service || 'Genel Tercüme';
    document.getElementById('modalDateVal').textContent = msg.created_at;
    document.getElementById('modalMessage').textContent = msg.message;
    document.getElementById('modalReplyBtn').href = 'mailto:' + msg.email + '?subject=İletişim Talebiniz Hakkında&body=Merhaba ' + msg.name + ',';
    
    modal.style.display = 'flex';
    
    // Mark as read in DB via AJAX (silent)
    if (msg.status == 0) {
        fetch('?toggle_status=' + msg.id);
        // İsteğe bağlı: UI'daki badge'i de değiştirebiliriz ama sayfa yenilenince düzeleceği için gerek yok
    }
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
    // Okundu işaretlendiyse sayıları güncellemek için sayfayı yenilemek mantıklı olabilir
    location.reload(); 
}

window.onclick = function(event) {
    const modal = document.getElementById('messageModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php include 'footer.php'; ?>
