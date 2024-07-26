<script>
    var generalStatuses = <?php echo json_encode($generalStatuses); ?>;
    var months = <?php echo json_encode($months); ?>;
</script>

<div class="chart-container processes-count-chart-container styled-box">
    <div class="processes-count-chart"></div>
    <span class="material-symbols-outlined chart-download-btn processes-count-chart__download-btn" title="Save as Image">download</span>
</div>
