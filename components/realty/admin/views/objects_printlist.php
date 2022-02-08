<script type="text/javascript">    
    window.print();
</script>
<div class="printlist">    
    <h1>Коммерческое предложение</h1>
    <div class="pl-agent">        
        <div class="dp75">            
            <ul>
                <li><img src="<?php echo $this->agent->agency->logo; ?>" alt="<?php echo $this->agent->agency->full_name; ?>"></li>
                <li><strong><?php echo $this->agent->agency->full_name; ?></strong></li>
                <li><strong><?php echo $this->agent->name; ?></strong> - <?php echo $this->agent->position; ?></li>
                <?php if (!empty($this->agent->phone)) : ?>
                    <li>Телефон для связи: <strong><?php echo $this->agent->phone; ?></strong></li>
                <?php endif; ?>
                <?php if (!empty($this->agent->work_email)) : ?>
                    <li>E-mail: <strong><?php echo $this->agent->work_email; ?></strong></li>
                <?php endif; ?>
                <?php if (!empty($this->agent->website)) : ?>
                    <li>Сайт: <strong><?php echo $this->agent->website; ?></strong></li>
                <?php endif; ?>
                <?php if (!empty($this->agent->agency->adress)) : ?>
                    <li><?php echo $this->agent->agency->adress; ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="dp25">
            <img src="<?php echo $this->agent->image; ?>" alt="" class="pl-agent-image">
        </div>
        <div class="clr"></div>
    </div>
    <?php $i = 1; foreach ($this->items as $key => $item) : $i++; ?>
        <div class="pl-object">
            <h2><?php echo $item->adress; ?> - <?php echo htmler::esc_price($item->price); ?></h2>
            <div class="pl-object-desc">
                <p><?php echo $item->comment; ?></p>
                <ul class="pl-object-params">
                    <li><strong>Тип дома:</strong> <?php echo $this->params['house_type'][$item->house_type]; ?></li>
                    <li><strong>Этаж:</strong> <?php echo $item->floor; ?> (всего <?php echo $item->floors; ?>)</li>
                    <li><strong>Угловая:</strong> <?php echo htmler::YesNo($item->param_uglovaya); ?></li>                                
                    <li><strong>Общая площадь:</strong> <?php echo $item->total_area; ?> кв. м</li>
                    <li><strong>Жилая площадь:</strong> <?php echo $item->living_area; ?> кв. м</li>
                    <li><strong>Площадь кухни:</strong> <?php echo $item->kitchen_area; ?> кв. м</li>
                    <li><strong>Сан. узел:</strong> <?php echo $this->params['wc_type'][$item->wc_type]; ?></li>                       
                    <li><strong>Лоджия:</strong> <?php echo $this->params['loggia_type'][$item->loggia_type]; ?></li>                    
                </ul>
                <div class="clr"></div>
            </div>
            <div class="pl-object-images">
                <?php $j = 0; foreach ($item->images as $image) : $j++; ?>
                    <?php if ($j > 4) break; ?>
                    <img src="<?php echo $this->config->realty_images_path.'thumbs/'.$image->image_name; ?>" alt=""  height="100" />
                <?php endforeach; ?>
            </div>
        </div>        
        <?php if ($i%3 == 0 && $i <= count($this->items)) : ?>
            <div class="pl-pagebreak"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>