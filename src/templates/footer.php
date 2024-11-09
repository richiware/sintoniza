<?php
require_once __DIR__ . '/../config.php';

function html_foot() {
	echo '</main>
		</div>

	<footer class="rounded-3 pt-4 pt-md-3 pb-4 pb-md-3 ps-4 pe-4 bg-secondary-subtle text-center">
		<p class="m-0">Instância gerenciada e mantida por <a class="link-secondary " href="https://pcdomanual.com/" target="_blank">PC do Manual</a> do <a class="link-secondary " href="https://manualdousuario.net" target="_blank">Manual do Usuario</a>.</p>
		<p class="m-0">Com ❤️ por <a class="link-secondary " href="https://altendorfme.com/" target="_blank">altendorfme</a> · Versão '.VERSION.'</p>
	</footer>
    </div>
	</body>
	</html> ';
}
