<script>
    var generalStatuses = <?php echo json_encode($generalStatuses); ?>;
    var months = <?php echo json_encode($months); ?>;
</script>

<div class="processes-count-chart-container styled-box">
    <div class="processes-count-chart"></div>
    <span class="material-symbols-outlined processes-count-chart__download-btn" title="Save as Image">download</span>
</div>

<div class="active-manufacturers-chart-container styled-box">
    <div class="active-manufacturers-chart"></div>
    <span class="material-symbols-outlined active-manufacturers-chart__download-btn" title="Save as Image">download</span>
</div>
