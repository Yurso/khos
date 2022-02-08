<?php foreach ($this->messages as $message) : ?>
    <div id="tm-item<?php echo $message->id; ?>" class="msg-item">
        <div class="msg-title">
            <?php echo htmlspecialchars($message->name); ?> 
        </div>   
        <div class="msg-date">
            <?php echo date("d.m.Y в H:i:s", strtotime($message->ts)); ?>
        </div> 
        <div class="msg-text">
            <?php echo nl2br($message->text); ?>
        </div>
        <a href="#" class="msg-show-more" style="display: none;">Показать полностью <i class="fas fa-caret-down"></i></a>
    </div>
<?php endforeach; ?>
