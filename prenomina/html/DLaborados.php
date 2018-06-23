<h4 style="text-align: center;"><?php echo $NomDep; ?></h4>
<h5 style="text-align: center;">DESCANSOS LABORADOS</h5>

<div class="row">
	<div class="col s12 m6 offset-m3 l4">
		<div role="form" onkeypress="return scriptChecadas(event)">
			<div>
				<label for="fch">Fecha</label>
				<input id="fch" class="Calend" type="text" value="<?php echo date("d/m/Y"); ?>" style="margin-left: 76px; width: 142px; font-size: 1rem; height: 1.5rem;">
				<br/>
				<label for="tiponom">Tipo de nomina</label>
				<input id="tiponom" type="number" min="1" max="6" name="tiponom" value="<?php echo $TN; ?>" style="margin-left: 19px; width: 142px; font-size: 1rem; height: 1.5rem;">
				<br/>
				<label for="Dep">Departamento</label>
				<input id="Dep" type="text" name="Dep" value="<?php echo $centro; ?>" style="margin-left: 24px; width: 142px; font-size: 1rem; height: 1.5rem;">
				<br/>
				<div class="input-field col s6" style="max-width: 211px;margin-left: 20px;">
					<input id="CE" type="text" class="validate" style="width: 164px; padding-top: 5px">
					<i class="material-icons prefix" onclick="DLaborados()" style="line-height: 39px;text-align: center;border: 1px solid rgba(0, 0, 0, .2);cursor:pointer;">search</i>
				</div>
				<br/>
				<div class="boton col s12 center-align" style="margin-top: 20px; margin-bottom: 50px;">
					<input class="btn" type="submit" value="BUSCAR" onclick="DLaborados()" id="btnT"/>
				</div>
				<br/>
				<br/>
			</div>
		</div>				
	</div>
	<div id="Stasis" class="row col s12 m6 l4 offset-l4">
				<form action="#">
					<div id="Stasis1" class="col s12 m6">
            <table class="striped centered">
              <thead>
                <tr>
                    <th>Activo</th>
                    <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                    <td>
											<p style="margin: 0;padding: 0;text-align: center;">
												<input type="radio" name="fecha" id="fecha1" value="0101">
												<label for="fecha1"></label>
											</p>
										</td>
                    <td>01/01</td>
                </tr>
                <tr>
										<td>
											<p style="margin: 0;padding: 0;text-align: center;">
												<input type="radio" name="fecha" id="fecha2" value="0502">
												<label for="fecha2"></label>
											</p>
										</td>
                    <td>05/02</td>
                </tr>
                <tr>
										<td>
											<p style="margin: 0;padding: 0;text-align: center;">
												<input type="radio" name="fecha" id="fecha3" value="1903">
												<label for="fecha3"></label>
											</p>
										</td>
                    <td>19/03</td>
                </tr>
                <tr>
										<td>
											<p style="margin: 0;padding: 0;text-align: center;">
												<input type="radio" name="fecha" id="fecha4" value="0105">
												<label for="fecha4"></label>
											</p>
										</td>
                    <td>01/05</td>
                </tr>
              </tbody>
            </table>					
        	</div>
					<!--/*id="Ncodigos"*/-->
					<div id="Stasis2" class="col s12 m6" >            
								<table id="Tfechas" class="striped centered">

								</table>
							<button id="NcodigosB" onClick="agregarFecha()" name="opcion" value="nuevo" class="waves-effect waves-light btn">Guardar</button>            
					</div>
				</form>
	</div>
</div>

<div class="modal " id="modal1" style="text-align: center; padding-top: 10px;">
  <h4 id="textCargado">Procesando...</h4>
  <div class="progress">
    <div class="indeterminate"></div>
  </div>
</div>
<div id="estado_consulta_ajax" style="height: auto;">

</div>
<script src="js/procesos/DLaborados.js" charset="utf-8"></script>
