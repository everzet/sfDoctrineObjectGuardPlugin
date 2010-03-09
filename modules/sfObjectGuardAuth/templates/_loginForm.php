<form action="<?php echo url_for('sf_object_guard_login') ?>" method="POST">
  <table>
    <?php echo $form ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="<?php echo __('Enter') ?>" />
      </td>
    </tr>
  </table>
</form>