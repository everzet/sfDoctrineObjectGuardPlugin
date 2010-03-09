<form action="<?php echo url_for('sf_object_guard_register') ?>" method="POST">
  <table>
    <?php echo $form ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="<?php echo __('Register') ?>" />
      </td>
    </tr>
  </table>
</form>