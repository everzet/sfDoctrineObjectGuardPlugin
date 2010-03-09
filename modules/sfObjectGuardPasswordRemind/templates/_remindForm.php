<form action="<?php echo url_for('sf_object_guard_password_remind') ?>" method="POST">
  <table>
    <?php echo $form ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="<?php echo __('Recover') ?>" />
      </td>
    </tr>
  </table>
</form>