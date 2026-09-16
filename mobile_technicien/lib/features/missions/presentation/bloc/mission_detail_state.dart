part of 'mission_detail_bloc.dart';

class MissionDetailState extends Equatable {
  final Mission mission;
  final bool processing;
  final String? errorMessage;

  const MissionDetailState({required this.mission, this.processing = false, this.errorMessage});

  MissionDetailState copyWith({Mission? mission, bool? processing, String? errorMessage}) {
    return MissionDetailState(
      mission: mission ?? this.mission,
      processing: processing ?? this.processing,
      errorMessage: errorMessage,
    );
  }

  @override
  List<Object?> get props => [mission, processing, errorMessage];
}
