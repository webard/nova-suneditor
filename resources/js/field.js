import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-suneditor', IndexField)
  app.component('detail-suneditor', DetailField)
  app.component('form-suneditor', FormField)
})
