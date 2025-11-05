<!-- Top Navbar -->
<header class="bg-white shadow flex justify-between items-center px-6 py-3">
  <h1 class="text-lg font-semibold text-[var(--primary)]">Dashboard</h1>
  <div class="text-sm text-gray-600">
    <?php if(!empty($_SESSION['admin_id'])): ?>
      <span class="font-medium">Admin</span>
    <?php elseif(!empty($_SESSION['lecturer_id'])): ?>
      <span class="font-medium">Lecturer: <?php echo  $_SESSION['lecturer_name'] ?></span>
    <?php endif; ?>
  </div>
</header>
