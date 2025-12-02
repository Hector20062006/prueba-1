<?php 
include("header.php"); 
?>

<!-- Contenedor principal de la página Quiénes Somos -->
<div class="about-container">

    <!-- Sección Hero (Título e Introducción) -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Cuidando tu salud, <br>valorando tu vida</h1>
            <p>Somos una institución médica comprometida con la excelencia, la tecnología de vanguardia y el trato humano. Más de 20 años al servicio de nuestra comunidad nos avalan.</p>
        </div>
    </section>

    <!-- Sección Misión y Visión -->
    <section class="mision-vision-container">
        <div class="mv-card">
            <h3>Nuestra Misión</h3>
            <p>Brindar atención médica integral de alta calidad, centrada en el paciente y su familia, combinando la excelencia clínica con la calidez humana para mejorar la calidad de vida de nuestra comunidad.</p>
        </div>
        <div class="mv-card">
            <h3>Nuestra Visión</h3>
            <p>Ser el hospital referente en la región, reconocido por nuestra innovación médica, nuestro equipo de especialistas altamente cualificados y nuestro compromiso inquebrantable con el bienestar del paciente.</p>
        </div>
    </section>

    <!-- Sección de Valores -->
    <section class="valores-section">
        <h2>Nuestros Valores</h2>
        <div class="valores-grid">
            <div class="valor-card">
                <div class="icono-valor">💙</div>
                <h4>Empatía</h4>
                <p>Tratamos a cada paciente como si fuera de nuestra propia familia.</p>
            </div>
            <div class="valor-card">
                <div class="icono-valor">🔬</div>
                <h4>Innovación</h4>
                <p>Invertimos constantemente en la mejor tecnología médica.</p>
            </div>
            <div class="valor-card">
                <div class="icono-valor">🤝</div>
                <h4>Integridad</h4>
                <p>Actuamos con ética, transparencia y honestidad en todo momento.</p>
            </div>
            <div class="valor-card">
                <div class="icono-valor">⭐</div>
                <h4>Excelencia</h4>
                <p>Buscamos la perfección en cada diagnóstico y tratamiento.</p>
            </div>
        </div>
    </section>

    <!-- Sección Breve de Equipo / Directiva -->
    <section class="equipo-section">
        <h2>Dirección Médica</h2>
        <div class="equipo-grid">
            <!-- Tarjeta Médico 1 -->
            <div class="miembro-card">
                <div class="foto-placeholder">Dr</div>
                <h4>Dr. Juan Pérez</h4>
                <span>Director General</span>
            </div>
            <!-- Tarjeta Médico 2 -->
            <div class="miembro-card">
                <div class="foto-placeholder">Dra</div>
                <h4>Dra. María López</h4>
                <span>Jefa de Cirugía</span>
            </div>
            <!-- Tarjeta Médico 3 -->
            <div class="miembro-card">
                <div class="foto-placeholder">Dr</div>
                <h4>Dr. Carlos Ruiz</h4>
                <span>Jefe de Urgencias</span>
            </div>
        </div>
    </section>

    <!-- Llamada a la acción -->
    <div class="cta-section">
        <h3>¿Necesitas atención médica?</h3>
        <p>Estamos disponibles las 24 horas para atenderte.</p>
        <a href="register.php" class="btn-cta">Regístrate Ahora</a>
    </div>

</div>

<?php include("footer.php"); ?>