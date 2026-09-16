part of 'notifications_bloc.dart';

sealed class NotificationsEvent extends Equatable {
  const NotificationsEvent();

  @override
  List<Object?> get props => [];
}

/// Chargement initial ou tirer-pour-rafraîchir — repart toujours de la page 1.
class NotificationsRequested extends NotificationsEvent {
  const NotificationsRequested();
}

/// Pagination (Étape 2.5) — charge la page suivante et l'ajoute à la liste.
class NotificationsMoreRequested extends NotificationsEvent {
  const NotificationsMoreRequested();
}

class NotificationMarkReadRequested extends NotificationsEvent {
  final String id;
  const NotificationMarkReadRequested(this.id);

  @override
  List<Object?> get props => [id];
}

class NotificationsMarkAllReadRequested extends NotificationsEvent {
  const NotificationsMarkAllReadRequested();
}
