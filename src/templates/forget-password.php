<?php
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $user = $gpodder->getUserByEmail($email);
    if ($user) {
        $resetToken = bin2hex(random_bytes(32));
        $gpodder->updatePasswordResetToken($user->id, $resetToken, time() + 1800);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = SMTP_AUTH;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;  
        $mail->Port = SMTP_PORT;
        $mail->setFrom(SMTP_FROM, TITLE);
        $mail->addAddress($email);
        $mail->Subject = TITLE . ' | Password Reset';
        $mail->Body = 'Please click the following link to reset your password: '.BASE_URL.'forget-password/reset?token=' . $resetToken;
        $mail->send();
        ?>
        <div class="alert alert-success" role="alert">
            <?php echo __('forget_password.email_sent'); ?>
        </div>
        <?php
    } else { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo __('forget_password.email_not_registered'); ?>
        </div>
    <?php }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <form method="post" action="/forget-password">
                    <h2 class="card-title text-center mb-4"><?php echo __('general.forgot_password'); ?></h2>
                    <div class="mb-3">
                        <label for="email" class="form-label"><?php echo __('general.email'); ?></label>
                        <input type="email" class="form-control" required name="email" id="email" />
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-envelope"></i> <?php echo __('general.send_reset_link'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
