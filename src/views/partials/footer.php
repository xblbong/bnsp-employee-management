<?php

declare(strict_types=1);
?>
</main>
</div>
</div>

<script src="<?= e(baseUrl('js/main.js')) ?>"></script>
<?php if (!empty($pageScripts) && is_array($pageScripts)): ?>
    <?php foreach ($pageScripts as $scriptPath): ?>
        <script src="<?= e(baseUrl($scriptPath)) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>

</html>