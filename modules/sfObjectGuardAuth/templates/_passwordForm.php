<form action="<?php echo url_for('sf_object_guard_password') ?>" method="POST">
  <table>
    <?php echo $form ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="<?php echo __('Set password') ?>" />
      </td>
    </tr>
  </table>
</form>