<?php $i=0; foreach($testimonials as $testimonial) : ?>
<div class="testimonial two-fourth <?php echo $i++ % 2 ? '' : 'last' ?>">
    <div class="thumbnail">
        <a class="name" href="<?php echo $testimonial['url'] ?>">
            <img title="" width="94" height="94" src="<?php echo $testimonial['thumbnail'] ?>" class="attachment-thumb_testimonial size-thumb_testimonial wp-post-image lazy-loaded"  data-src="<?php echo $testimonial['title'] ?>" data-lazy-type="image">
        </a>
    </div>
    <div class="testimonial-text">
        <?php echo $testimonial['content'] ?>
    </div>
    <div class="testimonial-name">
        <a class="name" href="<?php echo $testimonial['url'] ?>"><?php echo $testimonial['title'] ?></a>
    </div>
</div>
<?php endforeach; ?>
