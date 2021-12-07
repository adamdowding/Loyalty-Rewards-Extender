<h2>Oceanvape Rewards</h2>
<p>Here you can view your reward codes and points.</p>
<?php
echo "Your points: ". do_shortcode("[wr_simple_points]") . "/100";?>
<p>Your Oceanvape Level: <b><?php echo do_shortcode('[wr_user_level system="level_system" nolevel="No level yet"]');?></b></p>

<?php echo coupon_list(); 

