from orator import Model

class Feedback(Model):
    __table__ = 'feedback'
    __timestamps__ = False
    __fillable__ = ['nome', 'email', 'feedback']

    def validate(self):
        if "@" in self.email:
            return True
        else:
            self.error='Email deve conter @'
            return False

    def save(self, **kwargs):
        if self.validate():
          return super().save()
        return False




