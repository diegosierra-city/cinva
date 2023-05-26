export interface Visita {
 id: number
 cliente_id: number
 numero: number
 tecnico_id: number
 maquina_id: number
 fecha_solicitud: string
 fecha_programada: string
 hora_estimada: string
 fecha_servicio: string
 hora_inicio: string
 hora_final: string
 fecha_reporte: string
 descripcion_trabajo: string
 tipo_servicio: string
 reporte: string
 foto1: string
 foto2: string
 contador_negro: number
 contador_color: number
 valor: number
 estado: string
 fecha_pago: string
}

/*
 id: number
 nombre: string
 documento: string
 imagen: string
 email: string
 tipo: string
 activo: boolean

*/