<input
    type="hidden"
    id="upgradePremiumValidation"
    value="<?php echo wp_create_nonce('in_plugin_premium_upgrade'); ?>">

<div class="transferito-information">
    <div class="transferito-information__container transferito-information__container--large">
        <?php if (isset($data['iframeURL'])) : ?>
            <iframe
                id="upgradeToPremiumIFrame"
                src="<?php echo $data['iframeURL']; ?>"
                frameborder="0"
                height="480"
            ></iframe>
        <?php endif; ?>

        <div id="upgradeToPremiumAPIKeyEntry" class="transferito__hide-element">

            <div class="transferito__content-container transferito__content-container--no-margin">
                <div class="transferito-destination-url">

                    <div id="upgradeToPremiumErrorMessage" class="transferito__hide-element">
                        <?php echo loadTemplate( 'parts/notice', [
                            'type'              => 'error',
                            'message'           => 'We are unable to find an active subscription for the API Keys you provided, please re-enter your API Keys.',
                        ]); ?>
                    </div>


                    <div class="transferito-text__h1 ">Enter your API Keys</div>
                    <div class="transferito-destination-url__content--larger-margin transferito-text__p--regular">
                        To continue your migration, enter your public key and secret key in the fields below
                    </div>

                    <div class="transferito-destination-url__input--margin-top">
                        <div class="transferito-destination-url__title transferito-text__p1--bold">
                            <span class="transferito-input__required">*</span>
                            Enter your Public Key
                        </div>
                        <div class="transferito-destination-url__input">
                            <div class="transferito-input__dropdown-with-text">
                                <input
                                    id="upgradePremiumPublicKey"
                                    class="transferito-input__text-box transferito__field-required transferito-input__text-box--no-border transferito-input__text-box--full-width transferito-input__upgrade-premium-api-keys"
                                    type="text"
                                    placeholder="Enter your public key">
                            </div>
                        </div>
                    </div>

                    <div class="transferito-destination-url__input--margin-top">
                        <div class="transferito-destination-url__title transferito-text__p1--bold">
                            <span class="transferito-input__required">*</span>
                            Enter your Secret Key
                        </div>
                        <div class="transferito-destination-url__input">
                            <div class="transferito-input__dropdown-with-text">
                                <input
                                    id="upgradePremiumSecretKey"
                                    class="transferito-input__text-box transferito__field-required transferito-input__text-box--no-border transferito-input__text-box--full-width transferito-input__upgrade-premium-api-keys"
                                    type="text"
                                    placeholder="Enter your secret key">
                            </div>
                        </div>
                    </div>

                    <div class="transferito-destination-url__action-button">
                        <button id="updateYourAPIKeys" class="transferito-button transferito-button__primary transferito-button--small transferito_upgrade-save-api-keys" disabled>RESUME YOUR MIGRATION</button>
                    </div>
                </div>
            </div>

        </div>

        <div id="upgradeToPremiumLoading" class="transferito__hide-element"></div>

        <div id="upgradeToPremiumCheckComplete" class="transferito__hide-element">
            <?php echo loadTemplate( 'parts/notice', [
                'image'             => 'directory-success',
                'type'              => 'success',
                'messageTitle'      => 'Welcome to Transferito Premium!',
                'message'           => 'We have validated your API keys and have found an active subscription to Transferito Premium. You can now continue your migration.',
                'closeButton'       => true,
                'closeCssClasses'   => ['transferito_upgraded-in-app-resume-migration'],
                'additionalInfo'    => 'Click the close button above, to resume your migration.'
            ]); ?>
        </div>
    </div>
</div>
