<script type="text/javascript">    
    window.print();
</script>
<div class="printview">    
    <h1><?php echo $this->data->adress; ?></h1>
    <h2 style="font-size:12pt;font-weight:normal;margin-top:-20px;">(<?php echo $this->data->category_title; ?>)</h2>    
    <p style="font-size:12pt;"><strong>Цена:</strong> <?php echo htmler::esc_price($this->data->price); ?></p>
    <div class="pv-images">
        <?php foreach ($this->data->images as $key => $image) : ?>
            <img src="<?php echo $this->config->realty_images_path.'thumbs/'.$image->image_name; ?>" alt="" height="150">        
        <?php endforeach; ?>    
    </div>
    <p><?php echo $this->data->comment; ?></p>
    <div class="dp50">
        <ul>
            <li><strong>Тип дома:</strong> <?php echo $this->params['house_type'][$this->data->house_type]; ?></li>
            <li><strong>Этаж:</strong> <?php echo $this->data->floor; ?> (всего <?php echo $this->data->floors; ?>)</li>        
            <li><strong>Общая площадь:</strong> <?php echo $this->data->total_area; ?> кв. м</li>
            <li><strong>Жилая площадь:</strong> <?php echo $this->data->living_area; ?> кв. м</li>
            <li><strong>Площадь кухни:</strong> <?php echo $this->data->kitchen_area; ?> кв. м</li>
            <li><strong>Сан. узел:</strong> <?php echo $this->params['wc_type'][$this->data->wc_type]; ?></li>
            <li><strong>Лоджия:</strong> <?php echo $this->params['loggia_type'][$this->data->loggia_type]; ?></li>
            <li><strong>Угловая:</strong> <?php echo htmler::YesNo($this->data->param_uglovaya); ?></li>         
        </ul>
    </div>
    <div class="dp50">
        <ul>
            <li><strong>Трубы:</strong> <?php echo $this->params['param_pipes'][$this->data->param_pipes]; ?></li>
            <li><strong>Окна:</strong> <?php echo $this->params['param_windows'][$this->data->param_windows]; ?></li>
            <li><strong>Полы:</strong> <?php echo $this->params['param_flooring'][$this->data->param_flooring]; ?></li>
            <li><strong>Входная дверь:</strong> <?php echo $this->params['param_main_door'][$this->data->param_main_door]; ?></li>
            <li><strong>Межкомнатные двери:</strong> <?php echo $this->params['param_room_doors'][$this->data->param_room_doors]; ?></li>
            <li><strong>Статус/Правоустановка:</strong> <?php echo $this->data->rights; ?></li>
            <li><strong>Альтернатива/Прямая продажа:</strong> <?php echo $this->data->type_of_deal; ?></li>           
        </ul>
    </div>
    <div class="clr"></div>
    <p>
        <?php if (!empty($this->agent->name)) : ?>
            <strong>Агент:</strong> <?php echo $this->agent->name; ?><br />
        <?php endif; ?>
        <?php if (!empty($this->agent->phone)) : ?>
            <strong>Телефон:</strong> <?php echo $this->agent->phone; ?><br />
        <?php endif; ?>        
        <?php if (!empty($this->agent->work_email)) : ?>
            <strong>E-mail:</strong> <?php echo $this->agent->work_email; ?><br />
        <?php endif; ?>
        <br />
        <strong><?php echo $this->agent->agency->full_name; ?></strong><br />
        <?php echo $this->agent->agency->adress; ?>
    </p>
</div>