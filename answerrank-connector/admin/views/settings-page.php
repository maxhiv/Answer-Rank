<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="wrap arc-wrap">
    <h1>AnswerRank Connector</h1>
    <p class="description">Connect your site to the AnswerRank backend. This plugin only handles UI & auth. All heavy lifting happens in your backend.</p>

    <form method="post" action="options.php">
        <?php settings_fields('arc_settings_group'); ?>
        <?php $opts = AnswerRank_Connector::get_options(); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="backend_base_url">Backend Base URL</label></th>
                    <td>
                        <input name="arc_options[backend_base_url]" id="backend_base_url" type="url" class="regular-text" value="<?php echo esc_attr($opts['backend_base_url']); ?>" placeholder="https://your-backend.example/api" required />
                        <p class="description">Your Replit/AnswerRank API base (no trailing slash).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="site_api_key">Connector API Key</label></th>
                    <td>
                        <input name="arc_options[site_api_key]" id="site_api_key" type="text" class="regular-text" value="<?php echo esc_attr($opts['site_api_key']); ?>" placeholder="Paste the site key issued by your backend" />
                        <p class="description">Get this from your AnswerRank backend onboarding flow. Stored in wp_options.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="webhook_secret">Webhook Secret (optional)</label></th>
                    <td>
                        <input name="arc_options[webhook_secret]" id="webhook_secret" type="text" class="regular-text" value="<?php echo esc_attr($opts['webhook_secret']); ?>" />
                        <p class="description">If your backend posts webhooks to this site, verify signatures using this secret.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Connection Status</th>
                    <td>
                        <select name="arc_options[connection_status]" id="connection_status">
                            <?php foreach (['connected'=>'Connected','disconnected'=>'Disconnected','error'=>'Error'] as $k=>$v): ?>
                                <option value="<?php echo esc_attr($k); ?>" <?php selected($opts['connection_status'], $k); ?>><?php echo esc_html($v); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="button" id="arc-test-connection">Test Connection</button>
                        <span id="arc-conn-result" style="margin-left:8px;"></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rate Limiting</th>
                    <td>
                        <label>
                            Window (sec):
                            <input name="arc_options[rate_limit][window_sec]" type="number" min="10" step="1" value="<?php echo esc_attr(intval($opts['rate_limit']['window_sec'])); ?>" />
                        </label>
                        <label style="margin-left:12px;">
                            Max requests:
                            <input name="arc_options[rate_limit][max_requests]" type="number" min="1" step="1" value="<?php echo esc_attr(intval($opts['rate_limit']['max_requests'])); ?>" />
                        </label>
                        <p class="description">Applies to the plugin's REST proxy endpoints.</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>

    <hr/>

    <h2>Connect Flow</h2>
    <p>Use the button below to open the backend connect page (OAuth or custom token flow). After completing, paste the issued <strong>Connector API Key</strong> above.</p>
    <p>
        <a href="#" class="button button-primary" id="arc-open-connect">Open Connect</a>
    </p>
</div>
